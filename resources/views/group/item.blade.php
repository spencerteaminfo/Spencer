<template id="group-card-template">
    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 h-100 bg-white group-card js-card-wrapper" role="button" data-bs-toggle="modal" data-bs-target="#groupModal">

            <div class="ratio ratio-21x9 mb-3 rounded-3 overflow-hidden d-none js-img-wrapper">
                <img src="" class="w-100 h-100 object-fit-cover js-img" alt="{{ __('group.groupThumbnail') }}">
            </div>

            <span class="fw-bold text-secondary fs-6 js-name"></span>

            <div class="mt-2">
                <span class="badge bg-light text-primary rounded-pill">
                    <span class="js-role-owner d-none">{{ __('group.show.owner') }}</span>
                    <span class="js-role-cashier d-none">{{ __('group.show.cashier') }}</span>
                    <span class="js-role-member d-none">{{ __('group.show.member') }}</span>
                </span>
            </div>
        </div>
    </div>
</template>