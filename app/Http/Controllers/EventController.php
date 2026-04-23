<?php

namespace App\Http\Controllers;

use App\Enums\RoleType;
use App\Models\Attendance;
use App\Models\Event;
use App\Services\MembershipService;
use App\Services\SearchService;
use App\Services\StorageService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventController extends Controller
{
    protected SearchService $searchService;
    protected StorageService $storageService;
    protected MembershipService $membershipService;

    public function __construct(SearchService $userService, StorageService $storageService, MembershipService $membershipService)
    {
        $this->searchService = $userService;
        $this->storageService = $storageService;
        $this->membershipService = $membershipService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('events/index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('events/create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Show detail of specific event
     */
    public function show(Event $event): View|RedirectResponse
    {
        $user = auth()->user();
        $eventGroupIds = $event->groups()->pluck('groups.id');

        if (!$user->groups()->pluck('groups.id')->intersect($eventGroupIds)->count()) {
            return back();
        }

        return view('events.show', compact('event'));
    }

    /**
     * Search for both Users and Groups in one request, used for event assignment
     */
    public function searchUsersAndGroups(Request $request) : JsonResponse
    {
        $user = auth()->user();

        $groupIDs = $user->groups()->pluck('groups.id');
        $users = $this->searchService->users($request);
        $groups = $this->searchService->groups($request)
            ->whereIn('id', $groupIDs)
            ->values();

        return response()->json([
            'message' => 'Search was successful',
            'data' => [
                'users' => $users,
                'groups' => $groups
            ]
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'min:1']
        ]);

        $requester = auth()->user();
        $groupIDs = $requester->groups()->pluck('groups.id');

        if (!$request->filled('title')) {
            return response()->json($this->relatedEvents($requester, $groupIDs));
        }

        $events = Event::whereHas('groups', function($q) use ($groupIDs) {
            $q->whereIn('groups.id', $groupIDs);
        })
            ->where('title', 'like', '%' . $data['title'] . '%')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Search was successful',
            'data' => $events
        ], 200);
    }

    private function relatedEvents(Authenticatable $user, $groupIDs): Collection
    {
        return Event::whereHas('groups', function($q) use ($groupIDs) {
            $q->whereIn('groups.id', $groupIDs);
        })
            ->latest()
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:128'],
            'description' => ['nullable', 'string', 'max:60'],
            'deadline' => ['nullable', 'date'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date'],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:groups,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'img' => ['nullable', 'image', 'max:4096']
        ]);

        $groupIds = collect($data['group_ids'] ?? []);
        $userIds = collect($data['user_ids'] ?? []);

        if ($groupIds->isEmpty() && $userIds->isEmpty()) {
            throw ValidationException::withMessages([
                'targets' => ['You must provide either a group or an individual user.']
            ]);
        }

        $user = auth()->user();

        $event = Event::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'creator_id' => $user->id,
            'deadline' => $data['deadline'],
            'starts_at' => $data['from'],
            'ends_at' => $data['to'],
            'thumbnail_url' => $this->storageService->image($request->file('img'))
        ]);

        if ($groupIds->isNotEmpty()) $event->groups()->sync($groupIds->all());
        if ($userIds->isNotEmpty()) $event->users()->sync($userIds->all());

        $attendanceEntries = $this->collectAttendanceEntries($userIds, $event, $groupIds);

        Attendance::insert($attendanceEntries->unique(fn($i) => $i['user_id'].$i['group_id'])->toArray());

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event->load(['groups', 'users'])
        ], 201);
    }

    /**
     * Helper to format attendance row
     */
    private function makeAttendanceRow($eventId, $userId, $groupId = null): array
    {
        return [
            'event_id'   => $eventId,
            'user_id'    => $userId,
            'group_id'   => $groupId,
            'attends'    => 'PENDING',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $this->abortIfRequesterIsNotCreator($event);

        $data = $request->validate([
            'title'       => ['nullable', 'string', 'max:256'],
            'description' => ['nullable', 'string'],
            'deadline'    => ['nullable', 'date'],
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date'],
            'img'         => ['nullable', 'image', 'max:4096']
        ]);

        $imgPath = $event->thumbnail_url;
        if ($request->hasFile('img')) {
            $imgPath = $this->storageService->image($request->file('img'));
        }

        $event->update([
            'title'       => $data['title'] ?? $event->title,
            'description' => $data['description'] ?? $event->description,
            'deadline'    => $data['deadline'] ?? $event->deadline,
            'starts_at'   => $data['from'] ?? $event->starts_at,
            'ends_at'     => $data['to'] ?? $event->ends_at,
            'thumbnail_url' => $imgPath,
        ]);

        return response()->json(['message' => 'Event updated successfully', 'data' => $event], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        $this->abortIfRequesterIsNotCreator($event);

        $event->delete();
        return response()->json(['message' => 'Event was successfully destroyed', 'data' => $event->id], 200);
    }

    public function attendances(Event $event): JsonResponse
    {
        $attendances = $event->attendances()->with(['user', 'group'])->get();
        return response()->json(['message' => 'Success', 'data' => $attendances], 200);
    }

    /**
     * Post new group/user attendees to the event
     */
    public function storeAttendees(Request $request, Event $event): JsonResponse
    {
        $user = auth()->user();

        $attendance = Attendance::with('group')
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($attendance && $this->membershipService->hasAtLeastRole($user, $attendance->group, RoleType::CASHIER)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:groups,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id']
        ]);

        $groupIds = collect($data['group_ids'] ?? []);
        $userIds = collect($data['user_ids'] ?? []);

        $event->groups()->syncWithoutDetaching($groupIds->all());
        $event->users()->syncWithoutDetaching($userIds->all());

        $attendanceEntries = $this->collectAttendanceEntries($userIds, $event, $groupIds);

        $existing = Attendance::where('event_id', $event->id)->get(['user_id', 'group_id']);

        $toInsert = $attendanceEntries->filter(function($row) use ($existing) {
            return !$existing->where('user_id', $row['user_id'])->where('group_id', $row['group_id'])->first();
        });

        if ($toInsert->isNotEmpty()) {
            Attendance::insert($toInsert->toArray());
        }

        return response()->json(['message' => 'Attendees updated', 'data' => $event->load(['groups', 'users'])], 200);
    }

    /**
     * Delete requested group/user attendees from the event
     */
    public function destroyAttendees(Request $request, Event $event): JsonResponse
    {
        $user = auth()->user();

        $attendance = Attendance::with('group')
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($attendance && $this->membershipService->hasAtLeastRole($user, $attendance->group, RoleType::CASHIER)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:groups,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id']
        ]);

        $groupIds = collect($data['group_ids'] ?? []);
        $userIds = collect($data['user_ids'] ?? []);

        if ($groupIds->isNotEmpty()) {
            $event->groups()->detach($groupIds->all());
            $event->attendances()->whereIn('group_id', $groupIds->all())->delete();
        }

        if ($userIds->isNotEmpty()) {
            $event->users()->detach($userIds->all());
            $event->attendances()->whereIn('user_id', $userIds->all())->whereNull('group_id')->delete();
        }

        return response()->json(['message' => 'Removed', 'data' => $event->load(['groups', 'users'])], 200);
    }

    private function collectAttendanceEntries(\Illuminate\Support\Collection $userIds, Event $event, \Illuminate\Support\Collection $groupIds): \Illuminate\Support\Collection
    {
        $attendanceEntries = collect();

        foreach ($userIds as $userId) {
            $attendanceEntries->push($this->makeAttendanceRow($event->id, $userId, null));
        }

        $memberships = DB::table('memberships')->whereIn('group_id', $groupIds)->get();
        foreach ($memberships as $membership) {
            $attendanceEntries->push($this->makeAttendanceRow($event->id, $membership->user_id, $membership->group_id));
        }
        return $attendanceEntries;
    }

    private function abortIfRequesterIsNotCreator(Event $event): void
    {
        $user = auth()->user();

        if ($event->creator_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
