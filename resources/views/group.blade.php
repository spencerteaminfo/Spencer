<!doctype html>
<html lang="en">
<x-head title="Groups">
    @vite(['resources/js/group/showGroup.ts', 'resources/js/group/createGroup.ts'])
</x-head>
<body class="bg-light" data-bs-theme="{{ $activeTheme ?? 'light' }}">
<x-header />
<main class="d-flex">
    <x-sidebar/>
    <div id="content" class="flex-grow-1 p-3 p-md-5 overflow-auto">
        <div class="container-fluid">
            <div class="d-flex justify-content-center justify-content-md-start mb-5">
                <button class="btn btn-primary btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg d-flex align-items-center gap-2 border-0" data-bs-toggle="modal" data-bs-target="#groupModal" id="createNewGroupBtn">
                    <span class="fs-4 lh-1 text-white">+</span>
                    <span>Create New Group</span>
                </button>
            </div>

            <div id="groups-container" class="row g-4" data-user-id="{{ auth()->id() }}">
                <div class="col-12 text-center text-muted">Načítám skupiny...</div>
            </div>
        </div>
    </div>
</main>

<x-group.groupItem />
<x-group.groupCreate />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
