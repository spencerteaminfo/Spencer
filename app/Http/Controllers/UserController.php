<?php

namespace App\Http\Controllers;

use App\Events\Users\UserDeleted;
use App\Events\Users\UserProfileUpdated;
use App\Models\User;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $userService)
    {
        $this->searchService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function search(Request $request): JsonResponse
    {
        $users = $this->searchService->users($request);

        return response()->json([
            'message' => 'Search was successful',
            'data' => $users
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        return view('showUserProfile', compact('user'));
    }

    public function delete(Request $request): JsonResponse
    {
        $user = Auth::user();

        Auth::guard('web')->logout();

        $user->delete();

        event(new UserDeleted($user, $user));

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $userId = $user->id;
        return response()->json([
            'message' => 'Account deleted successfully.',
            'data' => $userId
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($request->has('delete_avatar')) {
            $user->update(['avatar_url' => null]);
            event(new UserProfileUpdated($user, ['avatar_url' => null]));
            return response()->json(['status' => 'success', 'path' => null]);
        }

        $data = $request->validate([
            'first_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('avatars', 'public');
            $data['avatar_url'] = $path;
            unset($data['profile_picture']);
        }

        $user->update($data);

        event(new UserProfileUpdated($user, $data));

        return response()->json([
            'message' => 'Profile Updated successfully.',
            'data' => $user
        ]);
    }
}
