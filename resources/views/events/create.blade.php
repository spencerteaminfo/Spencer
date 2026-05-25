<!doctype html>
<html lang="en">
<x-head title="Create Event">@vite(['resources/js/utils/showImg.ts', 'resources/js/event/createEvent.ts'])</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme }}">
<x-header />
<main class="d-flex">
    <x-sidebar/>
    <div id="content" class="flex-grow-1 p-3 p-md-5 overflow-auto">
        <div class="container-xl">
            <div class="row g-4 justify-content-center mb-5 mb-md-0">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                        <h2 class="h3 fw-bold mb-4 text-secondary">{{__('event.title')}}</h2>
                        <div id="title-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.title')}}</label>
                            <input id="input-title" type="text" class="form-control rounded-3" placeholder="{{__('event.create.title_placeholder')}}">
                        </div>

                        <div id="description-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.description')}}</label>
                            <textarea class="form-control rounded-3" rows="3" placeholder="{{__('event.create.description_placeholder')}}" id="input-description"></textarea>
                        </div>
                        <div class="row g-3 mb-4">
                            <div id="deadline-div" class="col-md-4">
                                <label class="form-label small text-muted">{{__('event.show.deadline')}}</label>
                                <input type="date" class="form-control rounded-3" id="input-deadline">
                            </div>
                            <div id="from-div" class="col-md-4">
                                <label class="form-label small text-muted">{{__('event.show.from')}}</label>
                                <input type="date" class="form-control rounded-3 border" id="input-from">
                            </div>
                            <div id="to-div" class="col-md-4">
                                <label class="form-label small text-muted">{{__('event.show.to')}}</label>
                                <input type="date" class="form-control rounded-3" id="input-to">
                            </div>
                        </div>
                        <div id="price-div" class="mb-3">
                            <label class="form-label small text-muted">{{__('event.show.price')}}</label>
                            <input type="number" class="form-control rounded-3" rows="3" placeholder="{{__('event.create.price_placeholder')}}" id="input-price"></input>
                        </div>
                        <div id="img-preview-div" class="ratio ratio-21x9 bg-light rounded-4 border border-secondary border-opacity-25 mb-2 position-relative">
                            <img id="img-preview" class="w-100 h-100 d-none top-0 start-0 rounded-4 z-1" style="object-fit: cover; pointer-events: none" alt="img-preview">
                            <label for="event-image-upload" class="d-flex flex-column justify-content-center align-items-center w-100 h-100" style="cursor: pointer;">
                                <img id="input-img" src="{{ Vite::asset('resources/svg/file.svg') }}" alt="Upload" class="opacity-50 mb-2" style="width: 80px; height: auto;">
                                <span class="small text-muted fw-bold">{{__('event.create.img_placeholder')}}</span>
                                <input type="file" id="event-image-upload" class="d-none" accept="image/png, image/jpg, image/webp, image/jpeg">
                            </label>
                        </div>
                        <input type="hidden" id="group-hidden" value='1'>
                        <button id="save-changes" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">{{__('event.create.submit')}}</button>
                    </div>

                    <div class="mb-4">
                        <div class="small text-muted text-end mb-2 mx-2">
                            <span>{{__('event.create.group_hint')}} <a href="/groups">{{__('event.create.group_action')}}</a></span>
                        </div>
                        <div class="position-relative">
                            <input id="searchInput" type="text" class="form-control rounded-pill py-3 px-4 shadow-sm border-0 mb-2" placeholder="{{__('event.create.search_placeholder')}}">
                            <span class="position-absolute end-0 top-50 translate-middle-y me-4">
                                <img src="{{ Vite::asset('resources/svg/search.svg') }}" alt="search" class="h-auto w-auto opacity-50">
                            </span>
                        </div>

                        <div id="userBulletList" class="d-flex flex-column gap-1 mb-2"></div>
                        <div id="groupSelectionError" class="text-danger small d-none px-2" data-message="{{__('event.create.group_required')}}"></div>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div id="addedMembers"></div>

                    </div>

                    <!-- <div class="d-flex gap-3 mt-4">
                        <button class="btn btn-primary flex-grow-1 rounded-pill py-2 fw-bold shadow-sm">I'm Interested</button>
                        <button class="btn btn-danger flex-grow-1 rounded-pill py-2 fw-bold shadow-sm">Not Interested</button>
                    </div> -->
                </div>


            </div>
        </div>
    </div>
</main>

<template id="event-group-search-item-template">
    <div class="add-user-btn p-3 border-bottom shadow-sm-hover cursor-pointer bg-white rounded-5 d-flex align-items-center gap-2 justify-content-between">
        <div class="d-flex align-items-center gap-2 min-w-0">
        <div class="rounded-circle overflow-hidden border border-secondary-subtle" style="width: 28px; height: 28px;">
            <img src="{{ Vite::asset('resources/svg/users.svg') }}" data-default-avatar="{{ Vite::asset('resources/svg/users.svg') }}" class="w-100 h-100 object-fit-cover js-avatar" alt="group">
        </div>
        <span class="small fw-bold js-name"></span>
        </div>
        <button type="button" class="btn btn-success btn-sm rounded-circle js-add-group d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; line-height: 1;">+</button>
    </div>
</template>

<template id="event-selected-group-item-template">
    <div class="card border border-success-subtle bg-success bg-opacity-10 rounded-3 px-3 py-2 mb-1 w-100">
        <div class="d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <div class="rounded-circle overflow-hidden border border-secondary-subtle" style="width: 28px; height: 28px; flex: 0 0 28px;">
                    <img
                        src="{{ Vite::asset('resources/svg/users.svg') }}"
                        data-default-avatar="{{ Vite::asset('resources/svg/users.svg') }}"
                        class="w-100 h-100 object-fit-cover js-avatar"
                        alt="group"
                    >
                </div>
                <span class="text-muted small text-truncate js-name"></span>
            </div>
            <div class="remove-user-btn text-danger small fw-bold px-1" role="button">✕</div>
        </div>
    </div>
</template>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
