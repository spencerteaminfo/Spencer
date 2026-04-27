<template id="event-card-compact-template">
    <a href="" class="text-decoration-none js-event-link">
        <div class="col-12 col-md-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-3">
                    <h5 class="mb-0 text-dark fw-bold text-truncate js-event-title"></h5>
                </div>
                <div class="card-footer bg-white border-0 py-3 px-3 mt-auto">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <img src="" alt="time" class="h-auto w-auto opacity-75 js-event-clock-icon">
                        <span>Deadline: <span class="js-event-deadline"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</template>

<template id="event-card-full-template">
    <a href="" class="text-decoration-none js-event-link">
        <div class="col-12 col-md-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-3">
                    <h5 class="mb-0 text-dark fw-bold text-truncate js-event-title"></h5>
                </div>
                <div class="text-muted mt-1 px-3">
                    <p class="mb-2 js-event-description"></p>
                </div>
                <div class="card-footer bg-white border-0 py-3 px-3 mt-auto">
                    <div class="d-flex align-items-center gap-2 text-muted small flex-wrap">
                        <img src="" alt="time" class="h-auto w-auto opacity-75 js-event-clock-icon">
                        <span>Deadline: <span class="js-event-deadline"></span></span>
                        <span>Start: <span class="js-event-start"></span></span>
                        <span>End: <span class="js-event-end"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</template>

