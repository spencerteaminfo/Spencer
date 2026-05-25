<!doctype html>
<html lang="{{__('home.language')}}">
<x-head title="Spencer">@vite(['resources/js/main/loadMainPage.ts', 'resources/js/group/loadGroup.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-header/>
<main class="d-flex">
    <x-sidebar/>
    <div id="content" class="flex-grow-1 p-3 p-md-5 mb-sm-5 mb-md-0 overflow-auto">
        <div class="container-fluid d-flex gap-4 flex-md-row flex-column mb-5 mb-md-0">
            <div class="column g-4 col-md-6">
                <h2 id="newest-event-title" class="mb-4">{{__('home.event.grid_name')}}</h2>
                <div id="container-events" data-url="{{ Vite::asset('resources/svg/clock.svg') }}"></div>
            </div>
            <div class="col-md-6 column g-4">
                <h2 class="mb-4">{{__('home.group.grid_name')}}</h2>
                <div id="container-groups" class="row g-3" data-url="{{ Vite::asset('resources/svg/users.svg') }}" data-empty-text="{{ __('home.group.empty') }}" data-error-text="{{ __('home.group.load_failed') }}"></div>
            </div>
        </div>
    </div>
</main>
@include('group.simple')
@include('events.templates')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
