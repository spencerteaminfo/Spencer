<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class SettingController extends Controller
{
    /**
     * Get a view of this resource's index.
     */
    public function showSettings(): Factory|View
    {
        $allSettings = Setting::with('options')->get();

        $currentSelect = Auth::user()->settings()->pluck('setting_options.id')->toArray();

        return view('settings', compact('allSettings', 'currentSelect'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'options' => 'nullable|array',
            'options.*' => 'exists:setting_options,id',
        ]);

        $optionsID = array_values($request->input('options', []));
        $user = Auth::user();
        // sync
        $user->settings()->sync($optionsID);


        $updatedSettings = $user->settings()->get();
        return response()->json([
            'message' => 'Settings updated successfully',
            'data' => $updatedSettings
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
