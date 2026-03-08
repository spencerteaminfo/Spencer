import api from './bootstrap';

let timeout: ReturnType<typeof setTimeout>;
let selectedUsers: { id: number, role: number }[] = [];
let currentGroupId: string | null = null;
let deletePictureFlag = false;

const modalList = document.getElementById('userBulletList') as HTMLElement | null;
const addedMembersContainer = document.getElementById('addedMembers') as HTMLDivElement | null;
const titleInput = document.getElementById('titleInput') as HTMLInputElement | null;
const saveBtn = document.getElementById('saveGroup') as HTMLButtonElement | null;
const deleteBtn = document.getElementById('deleteBtn') as HTMLButtonElement | null;
const searchInput = document.getElementById("searchInput") as HTMLInputElement | null;
const descriptionInput = document.getElementById("descriptionInput") as HTMLTextAreaElement | null;
const errorHandler = document.getElementById("errorHandler") as HTMLDivElement | null;
const groupModal = document.getElementById('groupModal');
const imageUploadInput = document.getElementById('event-image-upload') as HTMLInputElement | null;
const imgPreview = document.getElementById('img-preview') as HTMLImageElement | null;
const imgLabel = document.querySelector('label[for="event-image-upload"]') as HTMLElement | null;
const removeImgBtn = document.getElementById('remove-img-btn') as HTMLButtonElement | null;

const resetModal = () => {
    currentGroupId = null;
    selectedUsers = [];
    deletePictureFlag = false;

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
    if (imageUploadInput) imageUploadInput.value = '';
    if (imgPreview) { imgPreview.src = ''; imgPreview.classList.add('d-none'); }
    if (imgLabel) imgLabel.classList.remove('d-none');
    if (removeImgBtn) removeImgBtn.classList.add('d-none');
};

imageUploadInput?.addEventListener('change', (e) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file && imgPreview && imgLabel) {
        imgPreview.src = URL.createObjectURL(file);
        imgPreview.classList.remove('d-none');
        imgLabel.classList.add('d-none');
        if (removeImgBtn) removeImgBtn.classList.remove('d-none');
        deletePictureFlag = false;
    }
});

removeImgBtn?.addEventListener('click', () => {
    deletePictureFlag = true;
    if (imageUploadInput) imageUploadInput.value = '';
    if (imgPreview) { imgPreview.src = ''; imgPreview.classList.add('d-none'); }
    if (imgLabel) imgLabel.classList.remove('d-none');
    if (removeImgBtn) removeImgBtn.classList.add('d-none');
});

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

        const picUrl = button.getAttribute('data-picture');
        if (picUrl && picUrl !== 'null' && picUrl !== '') {
            if (imgPreview && imgLabel) {
                imgPreview.src = `/storage/${picUrl}`;
                imgPreview.classList.remove('d-none');
                imgLabel.classList.add('d-none');
                if (removeImgBtn && isCreator) removeImgBtn.classList.remove('d-none');
            }
        }

        if (!isCreator) {
            titleInput?.setAttribute('disabled', 'true');
            descriptionInput?.setAttribute('disabled', 'true');
            saveBtn?.classList.add('d-none');
            searchInput?.parentElement?.classList.add('d-none');
            if (imageUploadInput) imageUploadInput.disabled = true;
        } else {
            if (saveBtn) saveBtn.innerText = "Update Group";
            if (currentGroupId) deleteBtn?.classList.remove('d-none');
            if (imageUploadInput) imageUploadInput.disabled = false;
        }
    }
});

const addMemberToGroup = (user: any, canDelete: boolean = true) => {
    if (!addedMembersContainer) return;
    if (selectedUsers.some(u => u.id === user.id)) return;

    const defaultRole = user.role ? user.role : 4;
    selectedUsers.push({ id: user.id, role: defaultRole });

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
            ${canDelete ? `
            <select class="form-select form-select-sm border-0 bg-light role-select me-2" style="width: auto; font-size: 0.8rem;">
                <option value="4" ${defaultRole == 4 ? 'selected' : ''}>Member</option>
                <option value="6" ${defaultRole == 6 ? 'selected' : ''}>Cashier</option>
            </select>
            <div class="remove-user-btn text-danger small fw-bold px-1" role="button">✕</div>
            ` : ''}
        </div>`;

    if (canDelete) {
        card.querySelector('.role-select')?.addEventListener('change', (e) => {
            const newRole = parseInt((e.target as HTMLSelectElement).value);
            const target = selectedUsers.find(u => u.id === user.id);
            if (target) target.role = newRole;
        });
        card.querySelector('.remove-user-btn')?.addEventListener('click', () => {
            selectedUsers = selectedUsers.filter(u => u.id !== user.id);
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

        users.filter((u: any) => !selectedUsers.some(su => su.id === u.id)).forEach((user: any) => {
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

    const formData = new FormData();
    formData.append('name', titleValue);
    formData.append('description', descriptionInput?.value.trim() || '');

    selectedUsers.forEach(u => {
        formData.append('users_ids[]', u.id.toString());
        formData.append('role_ids[]', u.role.toString());
    });

    if (imageUploadInput?.files?.[0]) {
        formData.append('picture', imageUploadInput.files[0]);
    }

    if (deletePictureFlag) {
        formData.append('delete_picture', 'true');
    }

    try {
        saveBtn.disabled = true;
        saveBtn.innerText = "Saving...";
        const url = currentGroupId ? `/api/group/${currentGroupId}` : '/api/group';
        if (currentGroupId) {
            formData.append('_method', 'PATCH');
        }
        await api.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data' }});
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
        await api.delete(`/api/group/${currentGroupId}`);
        window.location.reload();
    } catch (e) {
        if (errorHandler) errorHandler.innerHTML = "Error deleting group.";
    }
});
