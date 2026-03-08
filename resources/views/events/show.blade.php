<!doctype html>
<html lang="en">
<x-head title="Show Event">@vite(['resources/js/showImg.ts', 'resources/js/createEvent.ts', 'resources/js/detailEvent.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-header />
<main class="d-flex">
    <x-sidebar/>
    <div id="content" class="flex-grow-1 p-3 p-md-5 overflow-auto">
        <div class="container-xl">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                        <h2 class="h3 fw-bold mb-4 text-secondary">ShowEvent</h2>
                        <div id="title-div" class="mb-3">
                            <label class="form-label small text-muted">Title</label>
                            <p>{{ $event->title }}</p>
                            {{--<p>{{ $event }}</p>--}} 
                        </div>

                        <div id="description-div" class="mb-3">
                            <label class="form-label small text-muted">Description</label>
                            <p>{{ $event->description }}</p>
                        </div>
                        <div class="row g-3 mb-4">
                            <div id="deadline-div" class="col-md-4">
                                <label class="form-label small text-muted">Deadline</label>
                                <p>{{ date('d.m.Y', strtotime($event->deadline))}}</p>
                            </div>
                            <div id="from-div" class="col-md-4">
                                <label class="form-label small text-muted">From</label>
                                <p>{{ date('d.m.Y', strtotime($event->starts_at))}}</p>
                            </div>
                            <div id="to-div" class="col-md-4">
                                <label class="form-label small text-muted">To</label>
                                <p>{{ date('d.m.Y', strtotime($event->ends_at))}}</p>
                            </div>
                        </div>
                        <div id="img-preview-div" class="ratio ratio-21x9 bg-light rounded-4 border border-secondary border-opacity-25 mb-2 position-relative">
                            {{$pathwayImg = $event->thumbnail_url}}
                            <img id="img-preview" src="{{  asset('storage/'.$pathwayImg)}}" class="w-100 h-100 top-0 start-0 rounded-4 z-1" style="object-fit: cover; pointer-events: none" alt="img-preview">
                            <label for="event-image-upload" class="d-flex flex-column justify-content-center align-items-center w-100 h-100">
                                <img id="input-img" src="{{ Vite::asset('resources/svg/file.svg') }}" alt="Upload" class="opacity-50 mb-2" style="width: 80px; height: auto;">
                            </label>
                        </div>
                        <input type="hidden" id="group-hidden" value='1'>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-muted">Groups:</h4>
                        <div id="userBulletList" class="d-flex flex-column gap-1 mb-2" data-groupid="{{$event->group_id}}"></div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div id="addedMembers"></div>

                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button class="btn btn-primary flex-grow-1 rounded-pill py-2 fw-bold shadow-sm">I'm Interested</button>
                        <button class="btn btn-danger flex-grow-1 rounded-pill py-2 fw-bold shadow-sm">Not Interested</button>
                    </div>
                </div>

                <div class="col-lg-4" id="attendance-panel">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                        <h2 class="h4 text-center fw-bold mb-4 text-secondary">Attendance</h2>

                        <div class="mb-4">
                            <span class="d-block small text-muted mb-3">Interested</span>
                            @foreach(range(1, 3) as $i)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2 flex-shrink-0" style="width: 24px; height: 24px;">
                                        <img src="{{ Vite::asset('resources/svg/user.svg') }}" class="w-100" alt="user">
                                    </div>
                                    <span class="small fw-medium flex-grow-1">John Doe</span>
                                    <div class="d-flex gap-2">
                                        <img src="{{ Vite::asset('resources/svg/check.svg') }}" class="opacity-100" alt="yes" role="button">
                                        <img src="{{ Vite::asset('resources/svg/x.svg') }}" class="opacity-25" alt="no" role="button">
                                        <img src="{{ Vite::asset('resources/svg/clock.svg') }}" class="opacity-25" alt="clock" role="button">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div>
                            <span class="d-block small text-muted mb-3">Not Interested</span>
                            @foreach(range(1, 3) as $i)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2 flex-shrink-0" style="width: 24px; height: 24px;">
                                        <img src="{{ Vite::asset('resources/svg/user.svg') }}" class="w-100" alt="user">
                                    </div>
                                    <span class="small fw-medium">John Doe</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
