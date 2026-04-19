<!doctype html>
<html lang="en">
<x-head title="User Profile"></x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme ?? 'light' }}">
<div>
    <x-header />
    <main class="d-flex">
        <x-sidebar class="z-3 position-absolute"/>
        <div id="content" class="flex-grow-1 overflow-auto mb-5">
            <div class="container position-relative mt-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10">
                        <div class="row align-items-center mb-5 g-4">

                            <div class="col-12 col-md-4 d-flex justify-content-center">
                                <div class="ratio ratio-1x1 w-75 bg-white rounded-circle shadow-sm border-white d-flex justify-content-center align-items-center overflow-hidden">
                                    <div class="d-flex justify-content-center align-items-center w-100 h-100">
                                        @php
                                            $fallback = 'https://ui-avatars.com/api/?name=' . urlencode($user->email) . '&background=198754&color=fff';
                                            $profilePic = $user->avatar_url ? asset('storage/' . $user->avatar_url) : $fallback;
                                        @endphp
                                        <img src="{{ $profilePic }}" class="w-100 h-100 rounded-circle border object-fit-cover" onerror="this.onerror=null;this.src='{{ $fallback }}';" alt="profile picture">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="card border-0 shadow-sm rounded-4 p-4">
                                    <h5 class="fw-bold text-secondary mb-4">{{__('setting.info')}}</h5>

                                    <div class="mb-3">
                                        <label class="form-label small text-muted ms-1">{{__('setting.first_name')}}</label>
                                        <div class="position-relative">
                                            <h4>{{$user->first_name}}</h4>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small text-muted ms-1">{{__('setting.last_name')}}</label>
                                        <div class="position-relative">
                                            <h4>{{$user->last_name}}</h4>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label small text-muted ms-1">{{__('setting.email')}}</label>
                                        <div class="position-relative">
                                            <h4>{{$user->email}}</h4>
                                        </div>
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
