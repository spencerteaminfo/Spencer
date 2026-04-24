<?php

namespace App\Http\Controllers;

use App\Enums\RoleType;
use App\Events\Events\EventCreated;
use App\Events\Events\EventUpdated;
use App\Events\Events\PaymentUpdated;
use App\Events\Events\UsersAddedToEvent;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Group;
use App\Models\Payment;
use App\Models\User;
use App\Services\MembershipService;
use App\Services\SearchService;
use App\Services\StorageService;
use Brick\Money\Money;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
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
    public function show(Event $event): View
    {
        $user = auth()->user();
        $eventGroupIds = $event->groups()->pluck('groups.id');
        $userGroupIds = $user?->groups()->pluck('groups.id') ?? collect();
        $isGroupMember = $eventGroupIds->intersect($userGroupIds)->isNotEmpty();
        $isDirectUser = $event->users()->whereKey($user?->id)->exists();

        if (!($user instanceof User) || (! $isGroupMember && ! $isDirectUser)) {
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
        })->latest()->get();
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
            'img' => ['nullable', 'image', 'max:4096'],
            'price_amount' => 'required|numeric',
            'price_currency' => 'required|string|size:3',
        ]);

        $groupIds = collect($data['group_ids'] ?? []);
        $userIds = collect($data['user_ids'] ?? []);

        if ($groupIds->isEmpty() && $userIds->isEmpty()) {
            throw ValidationException::withMessages([
                'targets' => ['You must provide either a group or an individual user.']
            ]);
        }

        $requester = auth()->user();

        $event = Event::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'creator_id' => $requester->id,
            'deadline' => $data['deadline'],
            'starts_at' => $data['from'],
            'ends_at' => $data['to'],
            'thumbnail_url' => $this->storageService->image($request->file('img')),
            'price' => Money::of($data['price_amount'], $data['price_currency'])
        ]);

        if ($groupIds->isNotEmpty()) $event->groups()->sync($groupIds->all());
        if ($userIds->isNotEmpty()) $event->users()->sync($userIds->all());

        $allUserIds = $this->aggregateUserIds($userIds, $groupIds);

        foreach ($allUserIds as $userId) {
            Attendance::create([
                'event_id' => $event->id,
                'user_id' => $userId,
            ]);

            Payment::create([
                'event_id' => $event->id,
                'user_id' => $userId,
            ]);
        }

        event(new EventCreated($event, $requester, $allUserIds->toArray()));

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

        event(new EventUpdated($event, auth()->user()));

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
        $attendances = $event->attendances()->with(['user'])->get();
        return response()->json([
            'message' => 'Success',
            'data' => $attendances
        ], 200);
    }

    public function payments(Event $event): JsonResponse
    {
        $payments = $event->payments()->with(['user'])->get();
        return response()->json([
            'message' => 'Success',
            'data' => $payments
        ], 200);
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

        if ($attendance && $event->hasUserAtLeastRole($user, RoleType::CASHIER)) {
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

        $allUserIds = $this->aggregateUserIds($userIds, $groupIds);

        foreach ($allUserIds as $userId) {
            Attendance::firstOrCreate([
                'event_id' => $event->id,
                'user_id' => $userId,
                'attends' => false
            ]);

            Payment::firstOrCreate([
                'event_id' => $event->id,
                'user_id' => $userId,
            ]);
        }

        event(new UsersAddedToEvent($event, auth()->user(), $allUserIds->toArray()));

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

        if (!($attendance && $event->hasUserAtLeastRole($user, RoleType::CASHIER))) {
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
            $event->payments()->whereIn('user_id', $userIds->all())->delete();
        }

        if ($userIds->isNotEmpty()) {
            $event->users()->detach($userIds->all());
            $event->attendances()->whereIn('user_id', $userIds->all())->whereNull('group_id')->delete();
            $event->payments()->whereIn('user_id', $userIds->all())->whereNull('group_id')->delete();
        }

        return response()->json([
            'message' => 'Removed',
            'data' => $event->load(['groups', 'users'])
        ], 200);
    }

    public function pay(Request $request, Event $event): JsonResponse
    {
        $requester = auth()->user();

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric'],
        ]);

        $attendance = Attendance::with('group')
            ->where('event_id', $event->id)
            ->where('user_id', $requester->id)
            ->first();

        if (!($attendance && $event->hasUserAtLeastRole($requester, RoleType::CASHIER))) {
            abort(403, 'Unauthorized action.');
        }

        $payment = $event->payments()
            ->where('user_id', $data['user_id'])
            ->where('event_id', $event->id)
            ->first();

        $payment->amount_paid += $data['amount'];

        $payment->save();

        event(new PaymentUpdated($payment, auth()->user()));

        return response()->json([
            'message' => 'Payment recorded',
            'data' => $payment
        ], 200);
    }

    public function setAmountPaid(Request $request, Event $event): JsonResponse
    {
        $requester = auth()->user();

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric'],
        ]);

        $attendance = Attendance::with('group')
            ->where('event_id', $event->id)
            ->where('user_id', $requester->id)
            ->first();

        if (!($attendance && $event->hasUserAtLeastRole($requester, RoleType::CASHIER))) {
            abort(403, 'Unauthorized action.');
        }

        $payment = $event->payments()
            ->where('user_id', $data['user_id'])
            ->where('event_id', $event->id)
            ->first();

        $payment->amount_paid = $data['amount'];

        $payment->save();

        event(new PaymentUpdated($payment, auth()->user()));

        return response()->json([
            'message' => 'Payment updated',
            'data' => $payment
        ], 200);
    }

    public function setPaid(Request $request, Event $event): JsonResponse
    {
        $requester = auth()->user();

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $attendance = Attendance::with('group')
            ->where('event_id', $event->id)
            ->where('user_id', $requester->id)
            ->first();

        if (!($attendance && $event->hasUserAtLeastRole($requester, RoleType::CASHIER))) {
            abort(403, 'Unauthorized action.');
        }

        $payment = $event->payments()
            ->where('user_id', $data['user_id'])
            ->where('event_id', $event->id)
            ->first();

        $payment->amount_paid = $event->price->getMinorAmount()->toInt();;

        $payment->save();

        event(new PaymentUpdated($payment, auth()->user()));

        return response()->json([
            'message' => 'Payment marked as paid fully',
            'data' => $payment
        ], 200);
    }

    private function abortIfRequesterIsNotCreator(Event $event): void
    {
        $user = auth()->user();

        if ($event->creator_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function aggregateUserIds($userIds, $groupIds): Collection
    {
        $groups = Group::whereIn('id', $groupIds)->with('users')->get();

        $userIdsFromGroups = $groups->flatMap(function ($group) {
            return $group->users->pluck('id');
        });

        return collect($userIds)
            ->merge($userIdsFromGroups)
            ->unique()
            ->values();
    }
}
