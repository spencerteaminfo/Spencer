<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $groupService)
    {
        $this->searchService = $groupService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $groups = auth()->user()->groups()->with('users')->get();
        return view('group', compact('groups'));
    }

    /**
     * Search in this resource
     */
    public function search(Request $request): JsonResponse
    {
        $groups = $this->searchService->groups($request);

        return response()->json([
            'message' => 'Search was successful',
            'data' => $groups
        ]);
    }

    /**
     * Store new resource.
     */
    public function store(Request $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:128'],
                'description' => ['nullable', 'string'],
                'users_ids' => ['nullable', 'array'],
                'users_ids.*' => ['exists:users,id'],
                'img' => ['nullable', 'image', 'max:4096']
            ]);

            $imgPath = null;
            if ($request->hasFile('img')) {
                $imgPath = $request->file('img')->store('thumbnails', 'public');
            }

            $group = Group::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'picture_url' => $imgPath ?? ''
            ]);

            $group->users()->attach(auth()->id(), [
                'role_id' => Role::first()->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $requester = auth()->user();

            if (!empty($data['users_ids'])) {
                foreach ($data['users_ids'] as $userId) {
                    if ($userId == $requester->id) continue;

                    $this->storeMember($userId, $group);
                }
            }

            return response()->json([
                'message' => 'Created',
                'data' => $group
            ], 200);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group): JsonResponse // TODO update members
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:128'],
            'description' => ['nullable', 'string'],
            'img' => ['nullable', 'image', 'max:4096']
        ]);

        $imgPath = null;
        if ($request->hasFile('img')) {
            $imgPath = $request->file('img')->store('thumbnails', 'public');
        }

        $requester = auth()->user();

        if (!$this->hasRole($requester, $group, 'owner') && !$this->hasRole($requester, $group, 'cashier')) {
            abort(403, 'Unauthorized action.');
        }

        $group->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'picture_url' => $imgPath ?? ''
        ]);

        return response()->json([
            'message' => 'Updated',
            'data' => $group
        ], 200);
    }

    /**
     * Add members to the specified group.
     */
    public function addMembers(Request $request, Group $group): JsonResponse
    {
        $data = $request->validate([
            'users_ids' => ['nullable', 'array'],
            'users_ids.*' => ['exists:users,id']
        ]);

        $requester = auth()->user();

        if (!$this->hasRole($requester, $group, 'owner') && !$this->hasRole($requester, $group, 'cashier')) {
            abort(403, 'Unauthorized action.');
        }

        $addedUserIds = [];

        if (!empty($data['users_ids'])) {
            foreach ($data['users_ids'] as $userId) {
                if ($userId == $requester->id) continue;
                if ($this->isMember($userId, $group)) continue;

                $this->storeMember($userId, $group);
                $addedUserIds[] = $userId;
            }
        }

        $newMembers = User::whereIn('id', $addedUserIds)->get();

        return response()->json([
            'message' => 'Members added',
            'data' => $newMembers
        ], 200);
    }

    /**
     * Remove members from the specified group.
     */
    public function destroyMembers(Request $request, Group $group): JsonResponse
    {
        $data = $request->validate([
            'users_ids' => ['nullable', 'array'],
            'users_ids.*' => ['exists:users,id']
        ]);

        if (!$this->hasRole(auth()->user(), $group, 'owner') && !$this->hasRole(auth()->user(), $group, 'cashier')) {
            abort(403, 'Unauthorized action.');
        }

        $group->users()->detach($data['users_ids']);

        return response()->json([
            'message' => 'Deleted',
            'data' => array_values($data['users_ids'])
        ], 200);
    }

    /**
     * Delete the specified group.
     */
    public function destroy(Group $group): JsonResponse
    {
        $group->delete();
        return response()->json([
            'message' => 'Deleted',
            'data' => $group->id
        ], 200);
    }

    // HELPER FUNCTIONS
    private function hasRole(Authenticatable $user, Group $group, string $roleName): bool
    {
        $membership = $this->userMembership($user, $group);

        return $membership && $membership->role->name === $roleName;
    }

    private function userMembership(Authenticatable $user, Group $group): ?Membership
    {
        return Membership::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->first();
    }

    private function isMember(int $userId, Group $group): bool
    {
        return Membership::where('user_id', $userId)
            ->where('group_id', $group->id)
            ->exists();
    }

    private function storeMember(int $userId, Group $group): void
    {
        $group->users()->attach($userId, [
            'role_id' => Role::first()->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
