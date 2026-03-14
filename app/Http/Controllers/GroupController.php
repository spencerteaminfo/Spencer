<?php

namespace App\Http\Controllers;

use App\Enums\RoleType;
use App\Models\Group;
use App\Models\Membership;
use App\Models\Role;
use App\Models\User;
use App\Rules\MapKeysExist;
use App\Rules\MatchUserIdsRule;
use App\Services\StorageService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupController extends Controller
{
    protected SearchService $searchService;
    protected StorageService $storageService;

    public function __construct(SearchService $groupService, StorageService $storageService)
    {
        $this->searchService = $groupService;
        $this->storageService = $storageService;
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
            $idsFromRequest = $request->input('users_ids', []);

            $data = $request->validate([
                'name' => ['required', 'string', 'max:128'],
                'description' => ['nullable', 'string'],
                'users_ids' => ['nullable', 'array'],
                'users_ids.*' => ['exists:users,id'],
                'users_roles' => ['nullable', 'array', new MatchUserIdsRule($idsFromRequest)],
                'users_roles.*' => ['exists:roles,id'],
                'img' => ['nullable', 'image', 'max:4096']
            ]);

            $group = Group::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'picture_url' => $this->storageService->image($request->file('img'))
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

                    $roleId = $data['users_roles'][$userId] ?? null;
                    $this->storeMember($userId, $group, $roleId);
                }
            }

            return response()->json([
                'message' => 'Created',
                'data' => $group
            ], 201);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group): JsonResponse // TODO update members
    {
        if (!$this->requesterHasAtLeastRole($group, RoleType::CASHIER)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:128'],
            'description' => ['nullable', 'string'],
            'img' => ['nullable', 'image', 'max:4096']
        ]);

        $group->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'picture_url' => $this->storageService->image($request->file('img'))
        ]);

        return response()->json([
            'message' => 'Updated',
            'data' => $group
        ], 200);
    }

    public function members(Group $group): JsonResponse
    {
        $users = $group->users()->get();

        return response()->json([
            'message' => 'Members were retrieved successfully.',
            'data' => $users
        ], 200);
    }

    /**
     * Add members to the specified group.
     */
    public function addMembers(Request $request, Group $group): JsonResponse
    {
        $requester = auth()->user();
        $requesterMembership = $this->userMembership($requester, $group);

        if (!$requesterMembership->hasAtLeastRole(RoleType::CASHIER)) {
            abort(403, 'Unauthorized action.');
        }

        $idsFromRequest = $request->input('users_ids', []);

        $data = $request->validate([
            'users_ids' => ['nullable', 'array'],
            'users_ids.*' => ['exists:users,id'],
            'users_roles' => ['nullable', 'array', new MatchUserIdsRule($idsFromRequest)],
            'users_roles.*' => ['exists:roles,id']
        ]);

        $addedUserIds = [];

        if (!empty($data['users_ids'])) {
            foreach ($data['users_ids'] as $userId) {
                if ($userId == $requester->id) continue;
                if ($this->isMember($userId, $group)) continue;

                $roleId = $data['users_roles'][$userId] ?? null;
                $this->storeMember($userId, $group, $roleId);
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
        if (!$this->requesterHasAtLeastRole($group, RoleType::CASHIER)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'users_ids' => ['nullable', 'array'],
            'users_ids.*' => ['exists:users,id']
        ]);

        $group->users()->detach($data['users_ids']);

        return response()->json([
            'message' => 'Deleted',
            'data' => array_values($data['users_ids'])
        ], 200);
    }

    public function updateMembers(Request $request, Group $group): JsonResponse
    {
        if (!$this->requesterHasAtLeastRole($group, RoleType::OWNER)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'users_roles' => ['required', 'array', new MapKeysExist('users', 'id')],
            'users_roles.*' => ['exists:roles,id']
        ]);

        $memberships = [];
        foreach ($data['users_roles'] as $userId => $roleId) {
            $memberships[] = Membership::where('user_id', $userId)
                ->where('group_id', $group->id)
                ->update(['role_id' => $roleId]);
        }

        return response()->json([
            'message' => "Updated users' roles",
            'data' => $memberships
        ], 200);
    }

    public function updateMember(Request $request, Group $group): JsonResponse
    {
        if (!$this->requesterHasAtLeastRole($group, RoleType::OWNER)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $membership = Membership::where('user_id', $data['user_id'])
            ->where('group_id', $group->id)
            ->update(['role_id' => $data['role_id']]);

        return response()->json([
            'message' => "Updated user's membership",
            'data' => $membership
        ], 200);
    }

    /**
     * Delete the specified group.
     */
    public function destroy(Group $group): JsonResponse
    {
        $requester = auth()->user();
        $requesterMembership = $this->userMembership($requester, $group);

        if (!$requesterMembership->hasAtLeastRole(RoleType::OWNER)) {
            abort(403, 'Unauthorized action.');
        }

        $group->delete();
        return response()->json([
            'message' => 'Deleted',
            'data' => $group->id
        ], 200);
    }

    private function requesterHasAtLeastRole(Group $group, RoleType $roleType): bool
    {
        $requesterMembership = $this->userMembership(auth()->user(), $group);
        return $requesterMembership->hasAtLeastRole($roleType);
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

    /**
     * Add a user to a group with a specific role.
     * Accepts either a role ID (int) or a RoleType Enum.
     */
    private function storeMember(int $userId, Group $group, int|RoleType|null $role = null): void
    {
        $role = $role ?? RoleType::MEMBER;

        if ($role instanceof RoleType) {
            static $roleCache = [];

            if (!isset($roleCache[$role->value])) {
                $roleCache[$role->value] = Role::findByType($role)->id;
            }

            $roleId = $roleCache[$role->value];
        } else {
            $roleId = $role;
        }

        $group->users()->attach($userId, [
            'role_id' => $roleId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
