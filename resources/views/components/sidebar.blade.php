<aside id="main-sidebar" class="bg-white d-none d-md-flex flex-column p-3 z-2">
    @vite(['resources/js/components/sidebar.ts'])

    <div class="d-md-flex flex-column gap-2">
        <a href="/" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
            <img src="{{ Vite::asset('resources/svg/home.svg') }}" alt="{{__('home.sidebar.home')}}" width="24" height="24">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.home')}}</span>
        </a>

        <div class="event-submenu-wrapper">
            <a href="/events" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
                <img src="{{ Vite::asset('resources/svg/list.svg') }}" alt="{{__('home.sidebar.event')}}" width="24" height="24">
                <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.event')}}</span>
            </a>
        </div>
            <a href="/groups" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
            <img src="{{ Vite::asset('resources/svg/users.svg') }}" alt="{{__('home.sidebar.groups')}}" width="24" height="24">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.groups')}}</span>
        </a>

        <a href="/settings" class="text-decoration-none d-flex align-items-center p-2 rounded hover-bg">
            <img src="{{ Vite::asset('resources/svg/settings.svg') }}" alt="{{__('home.sidebar.preferences')}}" width="24" height="24">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.preferences')}}</span>
        </a>
    </div>

    <div class="mt-auto d-flex flex-column gap-2 pt-3">
        <div class="d-flex align-items-center p-2 rounded cursor-pointer hover-bg" id="toggle-sidebar-btn">
            <img id="collapse-icon" src="{{ Vite::asset('resources/svg/arrow-left.svg') }}" alt="{{__('home.sidebar.collapse')}}">
            <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.collapse')}}</span>
        </div>
        <form id="logout" class="w-100">
            <button type="submit" class="btn border-0 bg-transparent w-100 text-start d-flex align-items-center p-2 rounded shadow-none hover-bg">
                <img src="{{ Vite::asset('resources/svg/log-out.svg') }}" alt="{{__('home.sidebar.collapse')}}" width="24" height="24">
                <span class="ms-3 text-secondary fw-medium sidebar-text">{{__('home.sidebar.logout')}}</span>
            </button>
        </form>
    </div>
</aside>

<nav class="navbar fixed-bottom bg-white border-top d-md-none p-2 shadow-lg">
    <div class="container-fluid px-0 h-100">
        <div class="d-flex w-100 h-100 justify-content-around align-items-center">
            <a href="/" class="text-center text-decoration-none flex-grow-1">
                <img src="{{ Vite::asset('resources/svg/home.svg') }}" alt="{{__('home.sidebar.home')}}" height="20">
                <div class="small text-muted">{{__('home.sidebar.home')}}</div>
            </a>
            <a href="/events" class="text-center text-decoration-none flex-grow-1">
                <img src="{{ Vite::asset('resources/svg/list.svg') }}" alt="{{__('home.sidebar.event')}}" height="20">
                <div class="small text-muted">{{__('home.sidebar.event')}}</div>
            </a>
            <a href="/groups" class="text-center text-decoration-none flex-grow-1">
                <img src="{{ Vite::asset('resources/svg/users.svg') }}" alt="{{__('home.sidebar.groups')}}" height="20">
                <div class="small text-muted">{{__('home.sidebar.groups')}}</div>
            </a>
            <a href="/settings" class="text-center text-decoration-none flex-grow-1">
                <img src="{{ Vite::asset('resources/svg/settings.svg') }}" alt="{{__('home.sidebar.preferences')}}" height="20">
                <div class="small text-muted">{{__('home.sidebar.preferences')}}</div>
            </a>
        </div>
    </div>
</nav>
