import api from './bootstrap';

let timeout: ReturnType<typeof setTimeout>;
let selectedUserIds: number[] = [];
let currentGroupId: string | null = null;

const modalList = document.getElementById('userBulletList') as HTMLElement | null;
const addedMembersContainer = document.getElementById('addedMembers') as HTMLDivElement | null;
const titleInput = document.getElementById('titleInput') as HTMLInputElement | null;
const saveBtn = document.getElementById('saveGroup') as HTMLButtonElement | null;
const deleteBtn = document.getElementById('deleteBtn') as HTMLButtonElement | null;
const searchInput = document.getElementById("searchInput") as HTMLInputElement | null;
const descriptionInput = document.getElementById("descriptionInput") as HTMLTextAreaElement | null;
const errorHandler = document.getElementById("errorHandler") as HTMLDivElement | null;
const groupModal = document.getElementById('groupModal');

const resetModal = () => {
    currentGroupId = null;
    selectedUserIds = [];
    if (titleInput) { titleInput.value = ''; titleInput.disabled = false; }
    if (descriptionInput) { descriptionInput.value = ''; descriptionInput.disabled = false; }
    if (addedMembersContainer) addedMembersContainer.innerHTML = '';
    if (modalList) modalList.innerHTML = '';
    if (errorHandler) errorHandler.innerHTML = '';
    if (deleteBtn) deleteBtn.classList.add('d-none');
    if (saveBtn) {
        saveBtn.innerText = "Save Group";
        saveBtn.disabled = false;
        saveBtn.classList.remove('d-none');
    }
    if (searchInput) {
        searchInput.value = '';
        searchInput.parentElement?.classList.remove('d-none');
    }
};

groupModal?.addEventListener('show.bs.modal', (event: any) => {
    const button = event.relatedTarget;
    resetModal();

    if (button.classList.contains('group-card')) {
        currentGroupId = button.getAttribute('data-id');
        const isCreator = button.getAttribute('data-is-creator') === 'true';

        if (titleInput) titleInput.value = button.getAttribute('data-name') || '';
        if (descriptionInput) descriptionInput.value = button.getAttribute('data-description') || '';
        const membersData = JSON.parse(button.getAttribute('data-members') || '[]');
        membersData.forEach((user: any) => addMemberToGroup(user, isCreator));

        if (!isCreator) {
            titleInput?.setAttribute('disabled', 'true');
            descriptionInput?.setAttribute('disabled', 'true');
            saveBtn?.classList.add('d-none');
            searchInput?.parentElement?.classList.add('d-none');
        } else {
            if (saveBtn) saveBtn.innerText = "Update Group";
            if (currentGroupId) deleteBtn?.classList.remove('d-none');
        }
    }
});

const addMemberToGroup = (user: any, canDelete: boolean = true) => {
    if (!addedMembersContainer) return;
    if (selectedUserIds.includes(user.id)) return;

    selectedUserIds.push(user.id);
    const [short] = user.email.split("@");
    const fallbackAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(short)}&background=198754&color=fff&size=128`;
    const hasLocalAvatar = user.avatar_url && user.avatar_url !== '' && !user.avatar_url.startsWith('http');
    const profilePic = hasLocalAvatar ? `/storage/${user.avatar_url}` : fallbackAvatar;
    const card = document.createElement('div');
    card.className = "card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100";
    card.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0" style="width: 32px;">
                <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                    <img src="${profilePic}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(short)}&background=198754&color=fff';">
                </div>
            </div>
            <div class="flex-grow-1 ms-2 small text-truncate">
                <span class="text-muted">${user.email}</span>
            </div>
            ${canDelete ? '<div class="remove-user-btn text-danger small fw-bold px-1" role="button">✕</div>' : ''}
        </div>`;

    if (canDelete) {
        card.querySelector('.remove-user-btn')?.addEventListener('click', () => {
            selectedUserIds = selectedUserIds.filter(id => id !== user.id);
            card.remove();
        });
    }
    addedMembersContainer.appendChild(card);
};

const performSearch = async (query: string) => {
    if (!modalList) return;
    if (query.length < 1) {
        modalList.innerHTML = '';
        return;
    }

    try {
        const response = await api.get("/api/users", {params: {email: query}});
        const users = response.data.data;
        modalList.innerHTML = "";

        users.filter((u: any) => !selectedUserIds.includes(u.id)).forEach((user: any) => {
            const [short, suffix] = user.email.split("@");
            const fallbackAvatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(short)}&background=198754&color=fff&size=128`;
            const hasLocalAvatar = user.avatar_url && user.avatar_url !== '' && !user.avatar_url.startsWith('http');
            const profilePic = hasLocalAvatar ? `/storage/${user.avatar_url}` : fallbackAvatar;

            const item = document.createElement('div');
            item.className = "d-flex align-items-center p-2 border-bottom shadow-sm-hover cursor-pointer rounded-4 mb-1 bg-white";
            item.role = "button";
            item.innerHTML = `
                <div class="flex-shrink-0" style="width: 40px;">
                    <div class="ratio ratio-1x1 rounded-circle overflow-hidden border">
                        <img src="${profilePic}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(short)}&background=198754&color=fff';">
                    </div>
                </div>
                <div class="flex-grow-1 ms-3 overflow-hidden">
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark small">${short}</span>
                        <span class="text-muted">@${suffix}</span>
                    </div>
                </div>
                <div class="text-primary fw-bold px-2">+</div>`;

            item.addEventListener('click', () => {
                addMemberToGroup(user);
                item.remove();
            });
            modalList.appendChild(item);
        });
    } catch (e) {
        console.error(e);
    }
};

searchInput?.addEventListener('input', (e) => {
    const val = (e.target as HTMLInputElement).value;
    clearTimeout(timeout);
    timeout = setTimeout(() => performSearch(val), 300);
});

saveBtn?.addEventListener("click", async () => {
    if (!errorHandler || !titleInput) return;
    const titleValue = titleInput.value.trim();
    if (!titleValue) { errorHandler.innerHTML = "Title is required"; return; }

    const data = {
        name: titleValue,
        description: descriptionInput?.value.trim() || '',
        users_ids: selectedUserIds
    };

    try {
        saveBtn.disabled = true;
        saveBtn.innerText = "Saving...";
        const url = currentGroupId ? `/group/edit/${currentGroupId}` : '/group/create';
        await api.post(url, data);
        window.location.reload();
    } catch (error: any) {
        saveBtn.disabled = false;
        saveBtn.innerText = currentGroupId ? "Update Group" : "Save Group";
        errorHandler.innerHTML = error.response?.data?.errors ? Object.values(error.response.data.errors).flat().join('<br>') : "Error saving group.";
    }
});

deleteBtn?.addEventListener("click", async () => {
    if (!currentGroupId || !confirm("Are you sure?")) return;
    try {
        await api.delete(`/group/delete/${currentGroupId}`);
        window.location.reload();
    } catch (e) {
        if (errorHandler) errorHandler.innerHTML = "Error deleting group.";
    }
});
