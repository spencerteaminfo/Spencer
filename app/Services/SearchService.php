<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Models\Event;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SearchService
{
    public function users(Request $request): Collection|array
    {
        $request->validate([
            'email' => ['nullable', 'string', 'min:1'],
        ]);

        $requester = auth()->user();

        if (!$request->filled('email')) {
            return $this->relatedUsers($requester);
        }

        $searchTerm = '%' . $request->email . '%';

        return User::where('id', '!=', $requester->id)
        ->where(function ($query) use ($searchTerm) {
            $query->whereLike('email', $searchTerm)
                ->orWhereLike('first_name', $searchTerm)
                ->orWhereLike('last_name', $searchTerm);
        })
            ->limit(10)
            ->get();
    }

    //TODO: implement actual logic
    private function relatedUsers(Authenticatable $user): array
    {
        return [];
    }

    public function groups(Request $request): Collection|array
    {
        $request->validate([
            'title' => ['nullable', 'string', 'min:1'],
        ]);

        $requester = auth()->user();

        if (!$request->filled('title')) {
            return $this->relatedGroups($requester);
        }
        return $requester->groups()
            ->whereLike('name', '%' . $request->title . '%')
            ->with('users')
            ->limit(10)
            ->get();
    }

    //TODO: implement actual logic
    private function relatedGroups(Authenticatable $user): Collection|array
    {
        return $user->groups()
            ->with('users')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function events(Request $request): Collection|Event
    {
        $request->validate([
            'title' => ['nullable', 'string', 'min:1'],
        ]);

        $requester = auth()->user();

        if (!$request->filled('title')) {
            return $this->relatedEvents($requester);
        }

        return Event::whereLike('title', '%' . $request->title . '%')
            ->limit(10)
            ->get();
    }

    private function relatedEvents(Authenticatable $user): array
    {
        return [];
    }
}
