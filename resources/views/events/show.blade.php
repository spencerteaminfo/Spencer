<!doctype html>
<html lang="en">
<x-head title="Show Event">@vite(['resources/js/event/detailEvent.ts'])
    <meta name="current-user-id" content="{{ auth()->user()->id }}">
</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-header />
@php
    $eventGroupId = $event->groups()->get();
@endphp
<main class="d-flex">
    <x-sidebar/>
    <div id="content" class="flex-grow-1 p-3 p-md-5 overflow-auto">
        <div class="container-xl">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                        <div id="title-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.title')}}</label>
                            <p>{{ $event->title }}</p>
                        </div>

                        <div id="description-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.description')}}</label>
                            <p>{{ $event->description }}</p>
                            <p>{{ $eventGroupId }}</p>
                        </div>
                        <div class="row g-3 mb-4">
                            <div id="deadline-div" class="col-md-4">
                                <label class="form-label small text-muted">{{__('event.show.deadline')}}</label>
                                <p>{{ date('d.m.Y', strtotime($event->deadline))}}</p>
                            </div>
                            <div id="from-div" class="col-md-4">
                                <label class="form-label small text-muted">{{__('event.show.from')}}</label>
                                <p>{{ date('d.m.Y', strtotime($event->starts_at))}}</p>
                            </div>
                            <div id="to-div" class="col-md-4">
                                <label class="form-label small text-muted">{{__('event.show.to')}}</label>
                                <p>{{ date('d.m.Y', strtotime($event->ends_at))}}</p>
                            </div>
                        </div>
                        <div id="img-preview-div" class="ratio ratio-21x9 bg-light rounded-4 border border-secondary border-opacity-25 mb-2 position-relative">
                            {{$pathwayImg = $event->thumbnail_url}}
                            @if(!empty($pathwayImg))
                                <img id="img-preview" src="{{  asset('storage/'.$pathwayImg)}}" class="w-100 h-100 top-0 start-0 rounded-4 z-1" style="object-fit: cover; pointer-events: none" alt="img-preview">
                            @endif
                            <label for="event-image-upload" class="d-flex flex-column justify-content-center align-items-center w-100 h-100">
                                <img id="input-img" src="{{ Vite::asset('resources/svg/file.svg') }}" alt="Upload" class="opacity-50 mb-2" style="width: 80px; height: auto;">
                            </label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-muted">{{__('event.show.groups')}}</h4>
                        <div id="userBulletList" class="d-flex flex-column gap-1 mb-2">
                            @foreach ($eventGroupId as $groupObj)
                            <div class="card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                                        <img src="{{ Storage::url('thumbnails/NXMQjsYT8PFHL1pheMhbi42i8S23Te576rMNIG4l.png') }}" class="w-100 profile-pic rounded-circle" style="max-width: 75px; max-height: 75px;" alt="acc">
                                    </div>
                                    <div class="small">
                                        <span class="text-muted d-none d-sm-inline">{{$groupObj->name}}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div id="addedMembers"></div>

                    </div>
                    <div id="interest-conteiner">
                        <div class="d-flex gap-3 mt-4">
                            <button id="interested" data-eventId="{{$event->id}}" class="btn btn-primary flex-grow-1 rounded-pill py-2 fw-bold shadow-sm">I'm Interested</button>
                            <button id="not-interested" class="btn btn-danger flex-grow-1 rounded-pill py-2 fw-bold shadow-sm">Not Interested</button>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4" id="attendance-panel">
                    <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                        <h2 class="h4 text-center fw-bold mb-4 text-secondary">{{__('event.show.attendance')}}</h2>
                        <div>
                            <span class="d-block small text-muted mb-3">{{__('event.show.are_interested')}}</span>
                            <div id="interested-container"></div>
                        </div>

                        <div>
                            <span class="d-block small text-muted mb-3">{{__('event.show.are_not_interested')}}</span>
                            <div id="not-interested-container">

                            </div>
                            {{--@foreach(range(1, 3) as $i)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2 shrink-0" style="width: 24px; height: 24px;">
                                        <img src="{{ Vite::asset('resources/svg/user.svg') }}" class="w-100" alt="user">
                                    </div>
                                    <span class="small fw-medium">John Doe</span>
                                </div>
                            @endforeach--}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
</body>
</html>
