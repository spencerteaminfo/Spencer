@vite(['resources/js/utils/searchMenu.ts'])
<header class="navbar navbar-light bg-white shadow position-sticky top-0 z-2">
    <div class="container-fluid">
        <div class="d-none d-md-flex" id="logo">
            <h1 class="mb-0 fw-bold text-primary">Spencer</h1>
        </div>
        <div class="d-flex d-md-none w-100 justify-content-between align-items-center h-100">
            <a href="/" class="text-decoration-none"><h1 class="mb-0 fw-bold text-primary">Spencer</h1></a>
            <div class="d-flex align-items-center gap-4" id="mobile-header-icons">
                <img id="mobileTrigger" src="{{ Vite::asset('resources/svg/search.svg') }}" alt="Search" class="icon-custom">

                <div class="position-relative">
                    <a href="/notifications">
                        <img src="{{ Vite::asset('resources/svg/bell.svg') }}" alt="Notifications" class="icon-custom">
                    </a>
                    <span class="position-absolute bottom-0 start-0 translate-middle p-1 bg-danger border border-white rounded-circle"></span>
                </div>

                <form id="mobileLogoutForm" method="POST" action="{{ route('api.logout') }}" class="m-0 d-inline">
                    @csrf
                    <button type="submit" class="btn p-0 border-0">
                        <img src="{{ Vite::asset('resources/svg/log-out.svg') }}" alt="Logout" class="icon-custom">
                    </button>
                </form>
            </div>
        </div>

        <div class="d-none d-md-flex position-absolute start-50 translate-middle-x align-items-center" id="search">
            <div class="position-relative">
                <input type="text" id="searchUserGroup" class="form-control rounded-pill border-secondary-subtle py-2 fs-5 px-4" autocomplete="off">
                <img src="{{ Vite::asset('resources/svg/search-input.svg') }}" class="position-absolute end-0 top-50 translate-middle-y me-3">
                <div id="searchResult" class="d-none position-absolute bg-white w-100" style="max-height: 300px; overflow-y: auto"></div>
            </div>
        </div>

        <div class="d-none d-md-flex align-items-center ms-auto" id="notifications">
            <a href="/notifications" class="text-decoration-none">
                <img src="{{ Vite::asset('resources/svg/bell.svg') }}" alt="Notifications">
            </a>
        </div>
    </div>
</header>

<div id="mobileSearchPopup" class="position-fixed top-0 start-0 z-3 w-100 d-none p-2">
    <div class="card rounded-pill shadow-sm">
        <div class="position-relative">
            <input type="text" id="mobileSearchInput" class="form-control rounded-pill border-secondary-subtle py-2 fs-5 px-4" autocomplete="off" placeholder="Search...">
            <img src="{{ Vite::asset('resources/svg/search-input.svg') }}" class="position-absolute end-0 top-50 translate-middle-y me-3">
            <div id="MobilesearchResult" class="position-absolute bg-white w-100 rounded-3 mt-1" style="max-height: 300px; overflow-y: auto"></div>
        </div>
    </div>
</div>

<div id="logoutOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark d-none d-flex align-items-center justify-content-center" style="z-index: 99999;">
    <div class="text-center text-white">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h2 class="fw-bold">{{ __('auth.logging_out') }}</h2>
        <p class="text-secondary small">{{ __('auth.wait_moment') }}</p>
    </div>
</div>

<template id="search-result-section-template">
    <div class="px-3 py-2 small text-uppercase fw-bold text-muted border-bottom js-label"></div>
</template>

<template id="search-user-item-template">
    <div class="p-3 border-bottom shadow-sm-hover">
        <a href="" class="d-flex align-items-center link-underline link-underline-opacity-0 w-100 js-link">
            <div class="flex-shrink-0" style="width: 45px;">
                <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                    <img src="" class="w-100 h-100 object-fit-cover js-avatar" alt="user">
                </div>
            </div>
            <div class="flex-grow-1 ms-3 overflow-hidden">
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark text-truncate js-short"></span>
                    <span class="text-muted small text-truncate js-suffix"></span>
                    <span class="text-secondary mt-1 small text-truncate js-full-name"></span>
                </div>
            </div>
        </a>
    </div>
</template>

<template id="search-group-item-template">
    <div class="p-3 border-bottom shadow-sm-hover">
        <a href="" class="d-flex align-items-center link-underline link-underline-opacity-0 w-100 js-link">
            <div class="flex-shrink-0" style="width: 45px;">
                <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                    <img src="" class="w-100 h-100 object-fit-cover js-avatar" alt="group">
                </div>
            </div>
            <div class="ms-3">
                <div class="fw-bold text-dark js-title"></div>
                <div class="text-muted small">Skupina</div>
            </div>
        </a>
    </div>
</template>

<template id="search-event-item-template">
    <div class="p-3 border-bottom shadow-sm-hover">
        <a href="" class="d-flex align-items-center link-underline link-underline-opacity-0 w-100 js-link">
            <div class="flex-shrink-0" style="width: 45px;">
                <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                    <img src="" class="w-100 h-100 object-fit-cover js-avatar" alt="event">
                </div>
            </div>
            <div class="flex-grow-1 ms-3 overflow-hidden">
                <div class="fw-bold text-dark text-truncate js-title"></div>
                <div class="text-muted small text-truncate">Událost</div>
            </div>
        </a>
    </div>
</template>

