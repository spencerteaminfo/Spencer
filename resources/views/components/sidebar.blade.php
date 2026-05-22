<aside id="main-sidebar" class="bg-white d-none d-md-flex flex-column p-3 z-2">
    @vite(['resources/js/components/sidebar.ts'])

    <div class="d-md-flex flex-column gap-2">
        <a href="/" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
            <img src="{{ Vite::asset('resources/svg/home.svg') }}" alt="Home" width="24" height="24">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.home')}}</span>
        </a>

        <div class="event-submenu-wrapper">
            <button type="button" class="btn border-0 d-flex align-items-center p-2 w-100 shadow-none hover-bg text-start bg-transparent" aria-expanded="false" id="event-menu-btn">
                <img src="{{ Vite::asset('resources/svg/plus-circle.svg') }}" alt="Event" width="24" height="24">
                <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.event')}}</span>
                <img src="{{ Vite::asset('resources/svg/chevron-down.svg') }}" alt="Toggle" width="16" height="16" class="ms-auto transition-rotate" id="event-chevron">
            </button>

            <div id="event-submenu" class="d-none ps-4 mt-1">
                <a href="/event/create" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
                    <img src="{{ Vite::asset('resources/svg/plus.svg') }}" alt="Create" width="20" height="20">
                    <span class="ms-3 text-secondary sidebar-text">{{__('home.sidebar.create_event')}}</span>
                </a>
                <a href="/events" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
                    <img src="{{ Vite::asset('resources/svg/list.svg') }}" alt="Show" width="20" height="20">
                    <span class="ms-3 text-secondary sidebar-text">{{__('home.sidebar.show_event')}}</span>
                </a>
            </div>
        </div>

        <a href="/groups" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
            <img src="{{ Vite::asset('resources/svg/users.svg') }}" alt="Groups" width="24" height="24">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.groups')}}</span>
        </a>

        <a href="/settings" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
            <img src="{{ Vite::asset('resources/svg/settings.svg') }}" alt="Settings" width="24" height="24">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.preferences')}}</span>
        </a>
    </div>

    <div class="mt-auto d-flex flex-column gap-2 pt-3">
        <div class="d-flex align-items-center p-2 rounded cursor-pointer hover-bg" id="toggle-sidebar-btn">
            <img id="collapse-icon" src="{{ Vite::asset('resources/svg/arrow-left.svg') }}" alt="Collapse">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.collapse')}}</span>
        </div>
        <form id="logout" class="w-100">
            <button type="submit" class="btn border-0 bg-transparent w-100 text-start d-flex align-items-center p-2 rounded shadow-none hover-bg">
                <img src="{{ Vite::asset('resources/svg/log-out.svg') }}" alt="Logout" width="24" height="24">
                <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.logout')}}</span>
            </button>
        </form>
    </div>
</aside>

<nav class="navbar fixed-bottom bg-white border-top d-md-none p-2 shadow-lg">
    <div class="container-fluid px-0 h-100">
        <div class="d-flex w-100 h-100 justify-content-around align-items-center">
            <a href="/" class="text-center text-decoration-none flex-grow-1">
                <img src="{{ Vite::asset('resources/svg/home.svg') }}" alt="Home" height="20">
                <div class="small text-muted">{{__('home.sidebar.home')}}</div>
            </a>

            <div class="flex-grow-1 position-relative">
                <button type="button" class="btn border-0 p-0 w-100 text-center bg-transparent" id="mobile-event-btn">
                    <img src="{{ Vite::asset('resources/svg/plus-circle.svg') }}" alt="Event" height="20">
                    <div class="small text-muted">{{__('home.sidebar.event')}}</div>
                </button>
                <div id="mobile-event-dropdown"
                     class="d-none position-absolute bottom-100 start-50 translate-middle-x mb-2 bg-white border rounded shadow p-2" style="min-width: 140px;">
                    <a href="/event/create" class="d-flex align-items-center gap-2 p-2 text-decoration-none text-dark hover-bg rounded">
                        <img src="{{ Vite::asset('resources/svg/plus.svg') }}" alt="Create" width="16" height="16">
                        <span class="small">{{__('home.sidebar.create_event')}}</span>
                    </a>
                    <a href="/events" class="d-flex align-items-center gap-2 p-2 text-decoration-none text-dark hover-bg rounded">
                        <img src="{{ Vite::asset('resources/svg/list.svg') }}" alt="Show" width="16" height="16">
                        <span class="small">{{__('home.sidebar.show_event')}}</span>
                    </a>
                </div>
            </div>

            <a href="/groups" class="text-center text-decoration-none flex-grow-1">
                <img src="{{ Vite::asset('resources/svg/users.svg') }}" alt="Groups" height="20">
                <div class="small text-muted">{{__('home.sidebar.groups')}}</div>
            </a>
            <a href="/settings" class="text-center text-decoration-none flex-grow-1">
                <img src="{{ Vite::asset('resources/svg/settings.svg') }}" alt="Settings" height="20">
                <div class="small text-muted">{{__('home.sidebar.preferences')}}</div>
            </a>
        </div>
    </div>
</nav>
