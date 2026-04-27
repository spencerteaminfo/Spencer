<template id="group-simple-template">
    <a href="" class="text-decoration-none js-link">
        <div class="col-12 col-md-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden shadow-sm-hover transition-all">

                <div class="card-header bg-white border-0 py-3 px-3 d-flex align-items-center gap-3">
                    <div class="ratio ratio-1x1 rounded-circle overflow-hidden border d-none js-img-wrapper" style="width: 40px;">
                        <img src="" class="w-100 h-100 object-fit-cover js-img" alt="{{ __('home.group.label') }}">
                    </div>

                    <h5 class="mb-0 text-dark fw-bold text-truncate js-name"></h5>
                </div>

                <div class="text-muted mt-1 px-3">
                    <p class="js-desc text-truncate mb-2" data-fallback="{{ __('home.group.no_description') }}"></p>
                </div>

                <div class="card-footer bg-white border-0 py-3 px-3 mt-auto">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <img src="" alt="{{ __('home.group.members') }}" class="h-auto w-auto opacity-75 js-icon" style="width: 16px; height: 16px;">
                        <span class="js-members-count">0</span>
                        <span>{{ __('home.group.members') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </a>
</template>
