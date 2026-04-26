<!doctype html>
<html lang="en">
<x-head title="Register">@vite(['resources/js/event/loadEvents.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-header/>
<main class="d-flex">
    <x-sidebar/>
    <div id="content" class="flex-grow-1 p-3 p-md-5 mb-sm-5 mb-md-0 overflow-auto">
        <div class="container-fluid d-flex gap-4 flex-md-row flex-column">
            <div class="column g-4 col-md-12">
                <h2 id="newest-event-title" class="mb-4">{{__('home.event.grid_name')}}</h2>
                <div id="container-events" data-url="{{ Vite::asset('resources/svg/clock.svg') }}"></div>
            </div>
        </div>
    </div>
</main>

@include('events.templates')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
