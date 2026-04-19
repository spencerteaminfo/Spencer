<!doctype html>
<html lang="en">
<x-head title="Settings">@vite(['resources/js/settingsUser.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<div id="deleteMenu" class="w-100 h-100 d-none position-fixed start-0 top-0 d-flex align-items-center bg-dark bg-opacity-50" style="z-index: 9999;">
    <div class="w-100 d-flex justify-content-center">
        <div class="card p-5 shadow-lg">
            <h2 class="text-muted">{{__('setting.delete')}}</h2>
            <form id="deleteAccountForm">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" id="submitDeleteBtn" class="btn btn-link link-danger p-0 text-decoration-none">
                        {{__('setting.delete_yes')}}
                        <img src="{{ Vite::asset('resources/svg/trash.svg') }}" class="mb-1" alt="Event" width="16" height="16">
                    </button>
                    <button type="button" id="cancelDelete" class="btn btn-primary">{{__('setting.delete_no')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div>
    <x-header />
    <main class="d-flex">
        <x-sidebar class="z-3 position-absolute"/>
        <div id="content" class="flex-grow-1 overflow-auto mb-5">
            <div class="container position-releative mt-5 mb-5 mb-md-0">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="row align-items-center mb-5 g-4">
                            <div class="col-12 col-md-4 d-flex justify-content-center">
                                <div id="profilePicContainer" class="cursor-pointer ratio ratio-1x1 w-75 bg-white rounded-circle shadow-sm border-white d-flex justify-content-center align-items-center overflow-hidden">
                                    <div class="d-flex justify-content-center align-items-center w-100 h-100">
                                        @php
                                            $profilePic = auth()->user()->avatar_url ? asset('storage/' . auth()->user()->avatar_url) : 'https://ui-avatars.com/api/?name=' . auth()->user()->email . '&background=198754&color=fff';
                                        @endphp
                                        <img id="avatarDisplay" src="{{ $profilePic }}" class="w-100 h-100 rounded-circle border object-fit-cover" alt="profile picture">
                                    </div>
                                    <p>Current Locale: {{ App::currentLocale() }}</p>
                                    <input type="file" id="profilePicInput" class="d-none" accept="image/*">
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="card border-0 shadow-sm rounded-4 p-4">
                                    <div id="saveSuccess" class="alert alert-success d-none py-1 small mb-2">Saved!</div>
                                    <form id="profileForm">
                                        <div class="mb-3">
                                            <label class="form-label small text-muted ms-1">{{__('setting.first_name')}}</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control rounded-3 pe-5" value="{{ auth()->user()->first_name ?? "" }}" name="first_name" id="firstName">
                                                <img id="editFirstName" src="{{ Vite::asset('resources/svg/edit.svg') }}" class="position-absolute end-0 top-50 translate-middle-y me-3 opacity-50 h-50 w-auto cursor-pointer">
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label small text-muted ms-1">{{__('setting.last_name')}}</label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control rounded-3 pe-5" value="{{ auth()->user()->last_name ?? "" }}" name="last_name" id="lastName">
                                                <img id="editLastName" src="{{ Vite::asset('resources/svg/edit.svg') }}" class="position-absolute end-0 top-50 translate-middle-y me-3 opacity-50 h-50 w-auto cursor-pointer">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-12 col-md-8 col-lg-6">
                                <div class="card border-0 shadow-sm rounded-4 p-4">
                                    <div class="text-center mb-4">
                                        <span class="h5 fw-bold text-secondary">{{__('setting.settings')}}</span>
                                        <hr class="mt-2 mb-0 opacity-25">
                                    </div>
                                    <form id="settingsForm">
                                        @csrf
                                        @foreach ($allSettings as $setting)
                                            @php
                                                $customLabels = [
                                                       'czech' => 'Čeština',
                                                       'english' => 'English',
                                                       'german' => 'Deutsch',
                                                       'theme' => 'Dark theme'
                                                   ];
                                               $settingName = $customLabels[$setting->name] ?? ucfirst($setting->name);
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                            <span class="fw-medium text-dark">
                                                {{ ucfirst(str_replace('_', ' ', $settingName)) }}
                                            </span>

                                                @php

                                                    $toggleOption = $setting->options->whereIn('option_data', ['dark', 'show', 'enable'])->first();
                                                    $isToggle = ($setting->options->count() === 2 && $toggleOption);
                                                @endphp

                                                @if ($isToggle)
                                                    <div class="form-check form-switch fs-4">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                               name="options[{{ $setting->id }}]"
                                                               value="{{ $toggleOption->id }}"
                                                            {{ in_array($toggleOption->id, $currentSelect) ? 'checked' : '' }}>
                                                    </div>
                                                @else
                                                    <div class="dropdown">
                                                        <select name="options[{{ $setting->id }}]" class="form-select form-select-sm setting-input border-primary text-primary fw-bold">
                                                            @foreach ($setting->options as $option)
                                                                @php
                                                                    $dName = $customLabels[$option->option_data] ?? ucfirst($option->option_data);
                                                                @endphp
                                                                <option value="{{ $option->id }}"
                                                                    {{ in_array($option->id, $currentSelect) ? 'selected' : '' }}>
                                                                    {{ $dName }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </form>
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="fw-medium text-dark">{{__('setting.delete_pfp')}}</span>
                                        <a href="#" id="DeletePFP" class="link-underline link-underline-opacity-0 link-danger link-underline-opacity-0-hover">{{__('setting.delete_pfp')}} <img src="{{ Vite::asset('resources/svg/trash.svg') }}" class="mb-1" alt="trashIcon" width="16" height="16"></a>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="fw-medium text-dark">{{__('setting.change_pass')}}</span>
                                        <a href="#" class="link-underline link-underline-opacity-0 link-primary link-underline-opacity-0-hover">{{__('setting.change_pass')}} <img src="{{ Vite::asset('resources/svg/edit-2.svg') }}" class="mb-1" alt="Event" width="16" height="16"></a>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="fw-medium text-dark">{{__('setting.delete_account')}}</span>
                                        <a href="#" id="openDeleteDialog" class="link-underline link-underline-opacity-0 link-danger link-underline-opacity-0-hover">{{__('setting.delete_account')}} <img src="{{ Vite::asset('resources/svg/trash.svg') }}" class="mb-1" alt="trashIcon" width="16" height="16"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
