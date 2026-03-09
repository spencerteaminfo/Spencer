import api from './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById("searchUserGroup") as HTMLInputElement | null;
    const searchResult = document.getElementById("searchResult") as HTMLElement | null;
    const mobileTrigger = document.getElementById("mobileTrigger") as HTMLElement | null;
    const mobileSearchPopup = document.getElementById("mobileSearchPopup") as HTMLElement | null;
    const mobileInput = document.getElementById("mobileSearchInput") as HTMLInputElement | null;
    const mobileSearchResult = document.getElementById("MobilesearchResult") as HTMLElement | null;

    if (!searchInput || !searchResult) return;

    const performSearch = async (query: string, resultContainer: HTMLElement) => {
        if (query.length < 1) {
            resultContainer.innerHTML = '';
            searchResult.classList.add("d-none");
            return;
        }

        try {
            const [resUser, resGroups, resEvents] = await Promise.all([
                api.get("/api/users", {params: { email: query }}),
                api.get("/api/groups", {params: { title: query }}),
                api.get("/api/events", {params: { title: query }})
            ]);

            const users = resUser.data.data;
            const groups = resGroups.data.data;
            const events = resEvents.data.data;

            resultContainer.innerHTML = "";

            if (users.length > 0 || groups.length > 0 || events.length > 0) {
                searchResult.classList.remove("d-none")
            }

            if (users.length > 0) {
                resultContainer.innerHTML += `<div class="px-3 py-2 small text-uppercase fw-bold text-muted border-bottom">Uživatelé</div>`;
                users.forEach((user: any) => {
                    const email = user.email || 'User';
                    const [short, suffix] = email.split("@");
                    const fullName = `${user.first_name ?? ''} ${user.last_name ?? ''}`.trim();
                    const fallbackAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(short)}&background=198754&color=fff&size=128`;
                    const hasLocalAvatar = user.avatar_url && user.avatar_url !== '' && !user.avatar_url.startsWith('http');
                    const profilePic = hasLocalAvatar ? `/storage/${user.avatar_url}` : fallbackAvatar;

                    resultContainer.innerHTML += `
                    <div class="p-3 border-bottom shadow-sm-hover">
                        <a href="/user/${user.id}" class="d-flex align-items-center link-underline link-underline-opacity-0 w-100"><div class="flex-shrink-0" style="width: 45px;">
                                <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                                    <img src="${profilePic}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(short)}&background=198754&color=fff';">
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3 overflow-hidden">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark text-truncate">${short}</span>
                                    <span class="text-muted small text-truncate">@${suffix}</span>
                                    ${fullName ? `<span class="text-secondary mt-1 small text-truncate">${fullName}</span>` : ''}
                                </div>
                            </div>
                        </a>
                    </div>`;
                });
            }

            if (groups.length > 0 || events.length > 0) {
                resultContainer.innerHTML += `<div class="px-3 py-2 small text-uppercase fw-bold text-muted border-bottom mt-2">Ostatní</div>`;

                groups.forEach((group: any) => {
                    resultContainer.innerHTML += `
                    <div class="p-3 border-bottom shadow-sm-hover">
                        <a href="" class="d-flex align-items-center link-underline link-underline-opacity-0 w-100">
                            <div class="flex-shrink-0" style="width: 45px;">
                                <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(group.name)}&background=198754&color=fff" class="w-100 h-100 object-fit-cover">
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold text-dark">${group.name}</div>
                                <div class="text-muted small">Skupina</div>
                            </div>
                        </a>
                    </div>`;
                });

                events.forEach((event: any) => {
                    resultContainer.innerHTML += `
                    <div class="p-3 border-bottom shadow-sm-hover">
                        <a href="/event/${event.id}" class="d-flex align-items-center link-underline link-underline-opacity-0 w-100">
                            <div class="flex-shrink-0" style="width: 45px;">
                                <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(event.title)}&background=198754&color=fff" class="w-100 h-100 object-fit-cover">
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3 overflow-hidden">
                                <div class="fw-bold text-dark text-truncate">${event.title}</div>
                                <div class="text-muted small text-truncate">Událost</div>
                            </div>
                        </a>
                    </div>`;
                });
            }
            if (!resultContainer.innerHTML) {
                searchResult.classList.add("d-none");
            }
        } catch (e) {
            console.error(e);
        }
    };

    searchInput.addEventListener('input', () => performSearch(searchInput.value, searchResult));
    mobileInput?.addEventListener('input', () => performSearch(mobileInput.value, mobileSearchResult!));

    mobileTrigger?.addEventListener('click', () => {
        mobileSearchPopup?.classList.remove('d-none');
        mobileInput?.focus();
    });

    document.addEventListener('click', (event: Event) => {
        const target = event.target as HTMLElement;
        if (!searchInput.contains(target) && !searchResult.contains(target)) {
            searchResult.innerHTML = '';
            searchResult.classList.add("d-none");
        }
        if (mobileSearchPopup && !mobileSearchPopup.contains(target) && !mobileTrigger?.contains(target)) {
            mobileSearchPopup.classList.add('d-none');
        }
    });
});
