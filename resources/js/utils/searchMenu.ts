import api from '../bootstrap';

const cloneTemplate = (id: string): HTMLElement | null => {
    const template = document.getElementById(id) as HTMLTemplateElement | null;
    if (!template) {
        return null;
    }

    return template.content.firstElementChild?.cloneNode(true) as HTMLElement | null;
};

const createSearchSectionTitle = (label: string, withTopMargin = false): HTMLElement | null => {
    const item = cloneTemplate('search-result-section-template');
    if (!item) {
        return null;
    }

    const labelEl = item.querySelector('.js-label');
    if (labelEl) labelEl.textContent = label;
    if (withTopMargin) item.classList.add('mt-2');
    return item;
};

const createUserResult = (user: any): HTMLElement | null => {
    const item = cloneTemplate('search-user-item-template');
    if (!item) {
        return null;
    }

    const email = user.email || 'User';
    const [short, suffix] = email.split('@');
    const fullName = `${user.first_name ?? ''} ${user.last_name ?? ''}`.trim();
    const fallbackAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(short)}&background=198754&color=fff&size=128`;
    const hasLocalAvatar = user.avatar_url && user.avatar_url !== '' && !user.avatar_url.startsWith('http');
    const profilePic = hasLocalAvatar ? `/storage/${user.avatar_url}` : fallbackAvatar;

    const linkEl = item.querySelector('.js-link') as HTMLAnchorElement | null;
    const imgEl = item.querySelector('.js-avatar') as HTMLImageElement | null;
    const shortEl = item.querySelector('.js-short') as HTMLElement | null;
    const suffixEl = item.querySelector('.js-suffix') as HTMLElement | null;
    const fullNameEl = item.querySelector('.js-full-name') as HTMLElement | null;

    if (linkEl) linkEl.href = `/user/${user.id}`;
    if (imgEl) {
        imgEl.src = profilePic;
        imgEl.onerror = () => {
            imgEl.onerror = null;
            imgEl.src = fallbackAvatar;
        };
    }
    if (shortEl) shortEl.textContent = short;
    if (suffixEl) suffixEl.textContent = `@${suffix ?? ''}`;
    if (fullNameEl) {
        if (fullName) {
            fullNameEl.textContent = fullName;
            fullNameEl.classList.remove('d-none');
        } else {
            fullNameEl.classList.add('d-none');
        }
    }

    return item;
};

const createGroupResult = (group: any): HTMLElement | null => {
    const item = cloneTemplate('search-group-item-template');
    if (!item) {
        return null;
    }

    const linkEl = item.querySelector('.js-link') as HTMLAnchorElement | null;
    const imgEl = item.querySelector('.js-avatar') as HTMLImageElement | null;
    const titleEl = item.querySelector('.js-title') as HTMLElement | null;

    if (linkEl) linkEl.href = `/groups?open=${group.id}`;
    if (imgEl) imgEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(group.name)}&background=198754&color=fff`;
    if (titleEl) titleEl.textContent = group.name ?? '';

    return item;
};

const createEventResult = (event: any): HTMLElement | null => {
    const item = cloneTemplate('search-event-item-template');
    if (!item) {
        return null;
    }

    const linkEl = item.querySelector('.js-link') as HTMLAnchorElement | null;
    const imgEl = item.querySelector('.js-avatar') as HTMLImageElement | null;
    const titleEl = item.querySelector('.js-title') as HTMLElement | null;

    if (linkEl) linkEl.href = `/event/${event.id}`;
    if (imgEl) imgEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(event.title)}&background=198754&color=fff`;
    if (titleEl) titleEl.textContent = event.title ?? '';

    return item;
};

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById("searchUserGroup") as HTMLInputElement | null;
    const searchResult = document.getElementById("searchResult") as HTMLElement | null;
    const mobileTrigger = document.getElementById("mobileTrigger") as HTMLElement | null;
    const mobileSearchPopup = document.getElementById("mobileSearchPopup") as HTMLElement | null;
    const mobileInput = document.getElementById("mobileSearchInput") as HTMLInputElement | null;
    const mobileSearchResult = document.getElementById("MobilesearchResult") as HTMLElement | null;
    const mobileLogoutForm = document.getElementById('mobileLogoutForm') as HTMLFormElement | null;
    const logoutOverlay = document.getElementById('logoutOverlay') as HTMLElement | null;

    if (mobileLogoutForm) {
        mobileLogoutForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (logoutOverlay) logoutOverlay.classList.remove('d-none');

            try {
                await api.post('/api/logout');
                window.location.href = '/login';
            } catch (e) {
                if (logoutOverlay) logoutOverlay.classList.add('d-none');
                console.error(e);
                window.location.href = '/login';
            }
        });
    }

    if (!searchInput || !searchResult) return;

    const performSearch = async (query: string, resultContainer: HTMLElement) => {
        if (query.length < 1) {
            resultContainer.innerHTML = '';
            resultContainer.classList.add("d-none");
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

            resultContainer.innerHTML = '';

            if (users.length > 0 || groups.length > 0 || events.length > 0) {
                resultContainer.classList.remove("d-none");
            } else {
                resultContainer.classList.add("d-none");
            }

            const fragment = document.createDocumentFragment();

            if (users.length > 0) {
                const usersTitle = createSearchSectionTitle('Uživatelé');
                if (usersTitle) fragment.appendChild(usersTitle);
                users.forEach((user: any) => {
                    const userResult = createUserResult(user);
                    if (userResult) fragment.appendChild(userResult);
                });
            }

            if (groups.length > 0 || events.length > 0) {
                const othersTitle = createSearchSectionTitle('Ostatní', true);
                if (othersTitle) fragment.appendChild(othersTitle);

                groups.forEach((group: any) => {
                    const groupResult = createGroupResult(group);
                    if (groupResult) fragment.appendChild(groupResult);
                });

                events.forEach((event: any) => {
                    const eventResult = createEventResult(event);
                    if (eventResult) fragment.appendChild(eventResult);
                });
            }

            resultContainer.appendChild(fragment);

            if (!resultContainer.innerHTML) {
                resultContainer.classList.add("d-none");
            }
        } catch (e) {
            console.error(e);
            resultContainer.classList.add("d-none");
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
