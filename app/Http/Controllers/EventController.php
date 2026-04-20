<?php

namespace App\Http\Controllers;

use App\Events\Events\AttendanceUpdated;
use App\Events\Events\EventCreated;
use App\Events\Events\EventUpdated;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\User;
use App\Services\SearchService;
use App\Services\StorageService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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

        if (!($user instanceof User) || !$user->groups->contains($event->group_id)) {
            abort(403, 'Unauthorized action.');
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
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:256'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date'],
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'img' => ['nullable', 'image', 'max:4096']
        ]);

        $event = Event::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'starts_at' => $data['from'],
            'ends_at' => $data['to'],
            'group_id' => $data['group_id'],
            'thumbnail_url' => $this->storageService->image($request->file('img')),
        ]);

        $memberships = $event->group->memberships;

        $attendanceIds = [];
        foreach ($memberships as $membership) {
            $attendance = Attendance::create([
                'membership_id'  => $membership->id,
                'event_id' => $event->id,
                'attends' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $attendanceIds[] = $attendance->id;
        }

        event(new EventCreated($event, auth()->user(), $attendanceIds));

        return response()->json([
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:256'],
            'description' => ['required', 'string'],
            'deadline'    => ['nullable', 'date'],
            'from'        => ['required', 'date'],
            'to'          => ['required', 'date'],
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

        event(new EventUpdated($event, auth()->user(), $data));

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $event
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
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
            event(new AttendanceUpdated($attendance, auth()->user()));
        }

        return response()->json([
            'message' => 'Attendance updated successfully',
            'data' => $attendance
        ], 200);
    }
}
