<!doctype html>
<html lang="en">
<x-head title="Notifications">@vite(['resources/js/notifications/notifications.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-header />

<main class="d-flex">
    <x-sidebar/>
    <div id="content" class="flex-grow-1 p-3 p-md-5 overflow-auto">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-4 px-2">
                        <h2 class="h4 fw-bold mb-0 text-secondary">{{__('notification.ui.title')}}</h2>
                        <button id="readAllButton" class="btn btn-sm btn-outline-primary rounded-pill px-3" type="button" data-label-read-all="{{__('notification.ui.read_all')}}">
                            {{__('notification.ui.read_all')}}
                        </button>
                    </div>
                    <p id="notificationsStatus" class="small text-muted px-2 mb-2 d-none" data-label-load-failed="{{__('notification.ui.load_failed')}}" data-label-single-read-fallback="{{__('notification.ui.single_read_fallback')}}" data-label-read-all-failed="{{__('notification.ui.read_all_failed')}}"></p>
                    <p id="notificationsEmpty" class="text-muted px-2 d-none mb-3">{{__('notification.ui.empty')}}</p>
                    <div id="notificationsList" class="d-flex flex-column gap-3" data-bell-icon="{{ Vite::asset('resources/svg/bell.svg') }}" data-label-read="{{__('notification.ui.read')}}" data-label-mark-as-read="{{__('notification.ui.mark_as_read')}}" data-label-unknown-time="{{__('notification.ui.unknown_time')}}" data-locale="{{ app()->getLocale() }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<template id="notification-item-template">
    <div class="card border-0 shadow-sm rounded-pill p-2 px-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <div class="ratio ratio-1x1 bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <img src="" alt="notif" class="h-50 w-auto opacity-75 js-bell-icon">
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <p class="mb-0 text-dark fw-medium text-truncate js-message"></p>
                <small class="text-muted opacity-75 js-time"></small>
            </div>
            <div class="ms-2 d-none d-sm-block">
                <button class="btn btn-sm btn-light rounded-pill border px-3 shadow-none notification-read-button" type="button"></button>
            </div>
        </div>
    </div>
</template>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
