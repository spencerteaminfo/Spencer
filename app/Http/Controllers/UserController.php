<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function show(User $user)
    {
        //
    }

    public function delete(Request $request): JsonResponse
    {
        $user = Auth::user();

        Auth::guard('web')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Account deleted successfully.',
            'data' => $user->id
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($request->has('delete_avatar')) {
            $user->update(['avatar_url' => null]);
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

        return response()->json([
            'message' => 'Profile Updated successfully.',
            'data' => $user
        ]);
    }
}
