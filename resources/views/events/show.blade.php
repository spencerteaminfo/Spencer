<!doctype html>
<html lang="en">
<x-head title="Show Event">@vite(['resources/js/event/detailEvent.ts'])
    <meta name="current-user-id" content="{{ auth()->user()->id }}">
    <meta name="current-event-id" content="{{ $event->id }}">
    <meta name="data-groups-ids" content="{{ json_encode($event->groups->pluck('id')) }}">
    <meta name="all-user-ids" content="{{ json_encode($event->users()->get()->groupBy('pivot.attends')) }}">
</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}" data-default-avatar="{{ Vite::asset('resources/svg/user.svg') }}">
<x-header />
@php
    $eventGroupId = $event->groups()->get();
@endphp
<main class="d-flex">
    <x-sidebar/>
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 shadow border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Upravit platbu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="modal-user-avatar" src="" class="rounded-circle border mb-2" style="width: 48px; height: 48px; object-fit: cover;">
                        <div id="modal-user-email" class="small fw-medium text-muted"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Částka (Kč)</label>
                        <input type="number" id="modal-amount-input" class="form-control form-control-lg rounded-3 text-center fw-bold" placeholder="0">
                    </div>
                    <input type="hidden" id="modal-user-id">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light w-100 rounded-pill" data-bs-dismiss="modal">Zrušit</button>
                    <button type="button" id="modal-save-btn" class="btn btn-success w-100 rounded-pill fw-bold">Uložit platbu</button>
                    <button type="button" id="modal-correct-btn" class="btn btn-success w-100 rounded-pill fw-bold">Zaplaceno přesne</button>
                </div>
            </div>
        </div>
    </div>
    <div id="content" class="flex-grow-1 p-3 p-md-5 overflow-auto">
        <div class="container-xl">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                        <div id="img-preview-div" class="ratio ratio-21x9 bg-light rounded-4 border border-secondary border-opacity-25 mb-2 position-relative">
                            {{$pathwayImg = $event->thumbnail_url}}
                            @if(!empty($pathwayImg))
                                <img id="img-preview" src="{{  asset('storage/'.$pathwayImg)}}" class="w-100 h-100 top-0 start-0 rounded-4 z-1" style="object-fit: cover; pointer-events: none" alt="img-preview">
                            @endif
                            <label for="event-image-upload" class="d-flex flex-column justify-content-center align-items-center w-100 h-100">
                                <img id="input-img" src="{{ Vite::asset('resources/svg/file.svg') }}" alt="Upload" class="opacity-50 mb-2" style="width: 80px; height: auto;">
                            </label>
                        </div>
                        <div id="title-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.title')}}</label>
                            <p>{{ $event->title }}</p>
                            @php
                                $people = $event->users()->get()->groupBy('pivot.attends');
                                $defaultAvatar = Vite::asset('resources/svg/user.svg');
                                $resolveAvatar = static function (?string $avatarUrl) use ($defaultAvatar): string {
                                    if (empty($avatarUrl)) {
                                        return $defaultAvatar;
                                    }

                                    if (\Illuminate\Support\Str::startsWith($avatarUrl, ['http://', 'https://', '/storage/'])) {
                                        return $avatarUrl;
                                    }

                                    return Storage::url($avatarUrl);
                                };
                            @endphp
                        </div>

                        <div id="description-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.description')}}</label>
                            <p>{{ $event->description }}</p>
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
                        <div id="price-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.price')}}</label>
                            <p>{{$event->price_amount}} {{$event->price_currency}}</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-muted">{{__('event.show.groups')}}</h4>
                        <div id="userBulletList" class="d-flex flex-column gap-1 mb-2">
                            @foreach ($eventGroupId as $groupObj)
                            @php
                                if(empty($groupObj->picture_url)){
                                    $groupImage = "https://ui-avatars.com/api/?name=".$groupObj->name."&background=198754&color=fff";
                                }else{
                                    $groupImage = Storage::url($groupObj->picture_url);
                                }

                            @endphp

                            <div class="card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                                        <img src="{{ $groupImage }}" class="w-100 profile-pic rounded-circle" style="max-width: 64px; max-height: 64px;" alt="acc">
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
                            <div id="interested-container">
                                @forelse($people->get(1) ?? [] as $user)
                                @php
                                    $avatarImage = $resolveAvatar($user->avatar_url);
                                @endphp
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2 shrink-0" style="width: 24px; height: 24px;">
                                            <img src="{{ $avatarImage }}" class="w-100" alt="user" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
                                        </div>
                                        <span class="small fw-medium">{{ $user->email }}</span>
                                        <div class="admin-only-info" data-user-id="{{ $user->id }}">
                                            <button id="button{{$user->id}}" class="btn btn-sm btn-light border rounded-pill d-flex align-items-center gap-1 open-payment-modal" 
                                                    data-email="{{ $user->email }}"
                                                    data-avatar="{{ $avatarImage }}">
                                                <span id="span{{$user->id}}" class="fw-bold text-success">0</span>
                                                <i class="bi bi-pencil-fill text-muted ms-1" style="font-size: 0.7rem;"></i>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-4">Zatím nikdo nepotvrdil účast.</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <span class="d-block small text-muted mb-3">{{__('event.show.are_not_interested')}}</span>
                            <div id="not-interested-container">
                                @forelse($people->get(0) ?? [] as $user)
                                @php
                                    $avatarImage = $resolveAvatar($user->avatar_url);
                                @endphp
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2 shrink-0" style="width: 24px; height: 24px;">
                                            <img src="{{ $avatarImage }}" class="w-100" alt="user" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
                                        </div>
                                        <span class="small fw-medium">{{ $user->email }}</span>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-2">Zatím nikdo nepotvrdil účast.</p>
                                @endforelse
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

<template id="event-detail-group-card-template">
    <div class="card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100">
        <div class="d-flex align-items-center">
            <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                <img src="https://ui-avatars.com/api/?name=Group&background=198754&color=fff" class="w-100 profile-pic" alt="group">
            </div>
            <div class="small">
                <span class="text-muted d-none d-sm-inline">Group</span>
            </div>
        </div>
    </div>
</template>

<template id="event-detail-attendance-item-template">
    <div class="d-flex align-items-center mb-3">
        <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2 shrink-0" style="width: 24px; height: 24px;">
            <img src="" class="w-100 js-avatar" alt="user" onerror="this.onerror=null;this.src='{{ $defaultAvatar ?? Vite::asset('resources/svg/user.svg') }}';">
        </div>
        <span class="small fw-medium js-email"></span>
    </div>
</template>
</body>
</html>
