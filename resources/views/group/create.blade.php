<div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg p-4">
            <div class="mb-3 d-flex justify-content-between align-items-start">
                <h5 class="fw-bold text-secondary mb-3" id="modalTitle">{{__('group.title')}}</h5>
                <img id="deleteBtn" class="cursor-pointer d-none" src="{{ Vite::asset('resources/svg/trash.svg') }}" alt="delete" width="20" height="20">
            </div>
            <div class="mb-3">
                <div id="img-preview-div" class="ratio ratio-21x9 bg-light rounded-4 border border-secondary border-opacity-25 mb-2 position-relative overflow-hidden">
                    <label for="event-image-upload" class="w-100 h-100 m-0 d-flex flex-column justify-content-center align-items-center" style="cursor: pointer;">
                        <img id="img-preview" class="w-100 h-100 d-none position-absolute top-0 start-0 z-1 object-fit-cover" alt="img-preview">

                        <div id="img-label-text" class="d-flex flex-column align-items-center z-0">
                            <img src="{{ Vite::asset('resources/svg/file.svg') }}" alt="Upload" class="opacity-50 mb-2" style="width: 80px; height: auto;">
                            <span class="small text-muted fw-bold">{{__('group.create.img_placeholder')}}</span>
                        </div>

                        <input type="file" id="event-image-upload" class="d-none" accept="image/png, image/jpg, image/webp, image/jpeg">
                    </label>
                    <button type="button" id="remove-img-btn" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 d-none z-2 rounded-circle fw-bold" style="width: 32px; height: 32px;">✕</button>
                </div>
            </div>
            <div class="mb-3">
                <input type="text" id="titleInput" class="form-control rounded-pill border-secondary-subtle px-4 py-2" placeholder="{{__('group.create.title_placeholder')}}">
                <textarea id="descriptionInput" class="form-control rounded-4 border-secondary-subtle px-4 py-2 mt-2" placeholder="{{__('group.create.description_placeholder')}}" rows="2"></textarea>
            </div>
            <div id="searchSection">
                <div class="position-relative mb-1">
                    <input type="text" id="searchInput" class="form-control rounded-pill border-secondary-subtle px-4 py-2" placeholder="{{__('group.create.search')}}">
                </div>
                <div id="userBulletList" class="d-flex flex-column gap-1 mb-2"></div>
            </div>
            <div class="mt-3">
                <label class="small fw-bold text-muted mb-2 ms-2">{{__('group.show.members')}}</label>
                <div id="addedMembers" class="d-flex flex-column gap-2"></div>
            </div>
            <div class="border-0 pt-4 d-flex flex-column gap-2">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal">{{__('group.show.close')}}</button>
                    <button type="button" id="saveGroup" class="btn btn-primary rounded-pill px-4 fw-bold flex-grow-1">{{__('group.create.submit')}}</button>
                </div>
                <div id="errorHandler" class="text-danger small text-center"></div>
            </div>
        </div>
    </div>
</div>

<template id="group-user-search-item-template">
    <div class="p-2 border-bottom shadow-sm-hover cursor-pointer bg-white">
        <span class="small fw-bold js-email"></span>
    </div>
</template>

<template id="group-member-item-template">
    <div class="d-flex justify-content-between align-items-center border p-2 rounded bg-white">
        <span class="small js-email"></span>
        <div class="d-flex align-items-center gap-2 js-actions">
            <select class="form-select form-select-sm role-select">
                <option value="4">Member</option>
                <option value="5">Cashier</option>
            </select>
            <span class="text-danger cursor-pointer remove-user">✕</span>
        </div>
    </div>
</template>

