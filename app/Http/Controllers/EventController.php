<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Services\SearchService;
use App\Services\StorageService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventController extends Controller
{
    protected SearchService $searchService;
    protected StorageService $storageService;

    public function __construct(SearchService $userService, StorageService $storageService)
    {
        $this->searchService = $userService;
        $this->storageService = $storageService;
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

    // FIXME this is now the exact same thing as show(Event $event).
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
    public function show(Event $event): View
    {
        $user = auth()->user();

        if (!$user->groups->contains($event->group_id)) {
            return back();
        }

        return view('events.show', compact('event'));
    }

    /**
     * Search for both Users and Groups in one request, used for event assignment
     */
    public function searchUsersAndGroups(Request $request) : JsonResponse
    {
        $groupIDs = auth()->user()->groups()->pluck('groups.id');
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

        $events = Event::with('group')
        ->whereIn('group_id', $groupIDs)
        ->whereLike('title', '%' . $data['title'] . '%')
        ->latest()
        ->get();

        return response()->json([
            'message' => 'Search was successful',
            'data' => $events
        ], 200);
    }
    private function relatedEvents(Authenticatable $user, $groupIDs): Collection
    {
        return Event::with('group')
        ->whereIn('group_id', $groupIDs)
        ->latest()
        ->get();
    }

    /**
     * Store a newly created resource in storage.
     * @throws ValidationException
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
                'targets' => ['You must provide either a group or an individual user to bind an event.']
            ]);
        }

        $event = Event::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'starts_at' => $data['from'],
            'ends_at' => $data['to'],
            'thumbnail_url' => $this->storageService->image($request->file('img'))
        ]);

        if (!$groupIds->isEmpty()) {
            $event->groups()->sync($groupIds);
        }
        if (!$userIds->isEmpty()) {
            $event->users()->sync($userIds);
        }

        $usersFromGroups = DB::table('memberships')
            ->whereIn('group_id', $groupIds)
            ->pluck('user_id');

        $allUniqueUserIds = $userIds->merge($usersFromGroups)->unique();

        $attendanceData = $allUniqueUserIds->map(fn($id) => [
            'user_id' => $id,
            'event_id' => $event->id,
            'attends' => 'PENDING',
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        Attendance::insert($attendanceData);

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event->load(['groups', 'users'])
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'title'       => ['nullable', 'string', 'max:256'],
            'description' => ['nullable', 'string'],
            'deadline'    => ['nullable', 'date'],
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date'],
            'img'         => ['nullable', 'image', 'max:4096']
        ]);

        if ($request->hasFile('img')) { // TODO img deletion!!!!!!
            $data['img_path'] = $request->file('img')->store('thumbnails', 'public');
        }

        $event->update([
            'title'       => $data['title'],
            'description' => $data['description'],
            'deadline'    => $data['deadline'],
            'starts_at'   => $data['from'],
            'ends_at'     => $data['to'],
            'thumbnail_url'    => $data['img_path'] ?? $event->img_path,
        ]);

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'message' => 'Event was successfully destroyed',
            'data' => $event->id
        ], 200);
    }

    public function attendances(Event $event): JsonResponse
    {
        $attendances = $event->attendances()->with('user')->get();

        return response()->json([
            'message' => 'Attendance of users was retrieved successfully.',
            'data' => $attendances
        ], 200);
    }

    /**
     * Set attendance of user for event
     */
    public function setAttendance(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'attends' => ['required', 'boolean']
        ]);

        $membership = Membership::where('user_id', $data['user_id'])
            ->where('group_id', $event->group_id)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'User is not a member of this group.',], 401);
        }

        $attendance = Attendance::where('event_id', $event->id)
            ->where('membership_id', $membership->id)
            ->first();

        if ($attendance) {
            $attendance->attends = $data['attends'];
            $attendance->save();
        }

        return response()->json([
            'message' => 'Attendance updated successfully',
            'data' => $attendance
        ], 200);
    }

    /**
     * Post new group/user attendees to the event
     * @throws ValidationException
     */
    public function storeAttendees(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:groups,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id']
        ]);

        $groupIds = collect($data['group_ids'] ?? []);
        $userIds = collect($data['user_ids'] ?? []);

        if ($groupIds->isEmpty() && $userIds->isEmpty()) {
            throw ValidationException::withMessages([
                'targets' => ['You must provide either a group or an individual user.']
            ]);
        }

        $event->groups()->sync($groupIds);
        $event->users()->sync($userIds);

        $usersFromGroups = DB::table('memberships')
            ->whereIn('group_id', $groupIds)
            ->pluck('user_id');

        $allTargetUserIds = $userIds->merge($usersFromGroups)->unique();

        $existingAttendanceIds = $event->attendances()->pluck('user_id')->toArray();
        $newUsersToInvite = $allTargetUserIds->diff($existingAttendanceIds);

        if ($newUsersToInvite->isNotEmpty()) {
            $attendanceData = $newUsersToInvite->map(fn($id) => [
                'user_id' => $id,
                'event_id' => $event->id,
                'attends' => 'PENDING',
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray();

            Attendance::insert($attendanceData);
        }

        $event->attendances()->whereNotIn('user_id', $allTargetUserIds)->delete();

        return response()->json([
            'message' => 'Attendees updated successfully',
            'data' => $event->load(['groups', 'users'])
        ], 200);
    }

    /**
     * Delete requested group/user attendees from the event
     * @throws ValidationException
     */
    public function destroyAttendees(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:groups,id'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id']
        ]);

        $groupIds = collect($data['group_ids'] ?? []);
        $userIds = collect($data['user_ids'] ?? []);

        if ($groupIds->isEmpty() && $userIds->isEmpty()) {
            throw ValidationException::withMessages([
                'targets' => ['You must provide at least one group or user to remove.']
            ]);
        }

        if ($groupIds->isNotEmpty()) {
            $event->groups()->detach($groupIds);
        }
        if ($userIds->isNotEmpty()) {
            $event->users()->detach($userIds);
        }

        $usersFromRemovedGroups = DB::table('memberships')
            ->whereIn('group_id', $groupIds)
            ->pluck('user_id');

        $allUserIdsToRemove = $userIds->merge($usersFromRemovedGroups)->unique();

        $event->attendances()->whereIn('user_id', $allUserIdsToRemove)->delete();

        return response()->json([
            'message' => 'Attendees removed successfully',
            'data' => $event->load(['groups', 'users'])
        ], 200);
    }
}
