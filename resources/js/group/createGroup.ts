import api from '../bootstrap';

let timeout: ReturnType<typeof setTimeout>;
let selectedUsers: { id: number, role: number }[] = [];
let initialUserIds: number[] = [];
let currentGroupId: string | null = null;
let deletePictureFlag = false;

const cloneTemplate = (id: string): HTMLElement | null => {
    const template = document.getElementById(id) as HTMLTemplateElement | null;
    if (!template) {
        return null;
    }

    return template.content.firstElementChild?.cloneNode(true) as HTMLElement | null;
};

document.addEventListener('DOMContentLoaded', () => {
    setupModalEvents();
    checkUrlForOpenGroup();
});

function setupModalEvents() {
    const groupModal = document.getElementById('groupModal');
    const imageUploadInput = document.getElementById('event-image-upload') as HTMLInputElement | null;
    const imgPreview = document.getElementById('img-preview') as HTMLImageElement | null;
    const imgLabelText = document.getElementById('img-label-text') as HTMLElement | null;
    const removeImgBtn = document.getElementById('remove-img-btn') as HTMLButtonElement | null;
    const searchInput = document.getElementById("searchInput") as HTMLInputElement | null;
    const saveBtn = document.getElementById('saveGroup') as HTMLButtonElement | null;
    const deleteBtn = document.getElementById('deleteBtn') as HTMLButtonElement | null;

    groupModal?.addEventListener('show.bs.modal', (event: any) => {
        const button = event.relatedTarget;
        resetModal();

        if (button && button.classList.contains('group-card')) {
            currentGroupId = button.dataset.id || null;
            const isCreator = button.dataset.isCreator === 'true';

            (document.getElementById('titleInput') as HTMLInputElement).value = button.dataset.name || '';
            (document.getElementById('descriptionInput') as HTMLTextAreaElement).value = button.dataset.description || '';

            const membersData = JSON.parse(button.dataset.members || '[]');
            initialUserIds = membersData.map((u: any) => u.id);
            membersData.forEach((user: any) => addMemberToGroup(user, isCreator));

            const picUrl = button.dataset.picture;
            if (picUrl && imgPreview && imgLabelText) {
                imgPreview.src = picUrl.startsWith('http') ? picUrl : `/storage/${picUrl}`;
                imgPreview.classList.remove('d-none');
                imgLabelText.classList.add('d-none');
                if (removeImgBtn && isCreator) removeImgBtn.classList.remove('d-none');
            }

            if (!isCreator) {
                document.getElementById('titleInput')?.setAttribute('disabled', 'true');
                document.getElementById('descriptionInput')?.setAttribute('disabled', 'true');
                saveBtn?.classList.add('d-none');
                document.getElementById('searchSection')?.classList.add('d-none');
                if (imageUploadInput) imageUploadInput.disabled = true;
            } else {
                if (saveBtn) saveBtn.innerText = "Update Group";
                if (currentGroupId) deleteBtn?.classList.remove('d-none');
                if (imageUploadInput) imageUploadInput.disabled = false;
            }
        }
    });

    imageUploadInput?.addEventListener('change', (e) => {
        const file = (e.target as HTMLInputElement).files?.[0];
        if (file && imgPreview && imgLabelText) {
            imgPreview.src = URL.createObjectURL(file);
            imgPreview.classList.remove('d-none');
            imgLabelText.classList.add('d-none');
            if (removeImgBtn) removeImgBtn.classList.remove('d-none');
            deletePictureFlag = false;
        }
    });

    removeImgBtn?.addEventListener('click', () => {
        deletePictureFlag = true;
        if (imageUploadInput) imageUploadInput.value = '';
        if (imgPreview) { imgPreview.src = ''; imgPreview.classList.add('d-none'); }
        if (imgLabelText) imgLabelText.classList.remove('d-none');
        if (removeImgBtn) removeImgBtn.classList.add('d-none');
    });

    searchInput?.addEventListener('input', (e) => {
        const val = (e.target as HTMLInputElement).value;
        clearTimeout(timeout);
        timeout = setTimeout(() => performSearch(val), 300);
    });

    saveBtn?.addEventListener('click', async () => saveGroupData());
    deleteBtn?.addEventListener('click', async () => deleteGroupData());
}

function resetModal() {
    currentGroupId = null;
    selectedUsers = [];
    initialUserIds = [];
    deletePictureFlag = false;

    const title = document.getElementById('titleInput') as HTMLInputElement;
    const desc = document.getElementById('descriptionInput') as HTMLTextAreaElement;
    if (title) { title.value = ''; title.disabled = false; }
    if (desc) { desc.value = ''; desc.disabled = false; }

    document.getElementById('addedMembers')!.innerHTML = '';
    document.getElementById('userBulletList')!.innerHTML = '';
    document.getElementById('errorHandler')!.innerHTML = '';
    document.getElementById('searchSection')?.classList.remove('d-none');
    document.getElementById('deleteBtn')?.classList.add('d-none');

    const saveBtn = document.getElementById('saveGroup') as HTMLButtonElement;
    if (saveBtn) { saveBtn.innerText = "Save Group"; saveBtn.disabled = false; saveBtn.classList.remove('d-none'); }
    const imgLabelText = document.getElementById('img-label-text');
    const imgPreview = document.getElementById('img-preview') as HTMLImageElement;
    const removeImgBtn = document.getElementById('remove-img-btn');
    if (imgLabelText) imgLabelText.classList.remove('d-none');
    if (imgPreview) { imgPreview.src = ''; imgPreview.classList.add('d-none'); }
    if (removeImgBtn) removeImgBtn.classList.add('d-none');
}

async function performSearch(query: string) {
    const modalList = document.getElementById('userBulletList');
    if (!modalList) return;
    if (query.length < 1) { modalList.innerHTML = ''; return; }

    try {
        const response = await api.get("/api/users", {params: {email: query}});
        modalList.innerHTML = "";
        response.data.data.filter((u: any) => !selectedUsers.some(su => su.id === u.id)).forEach((user: any) => {
            const item = cloneTemplate('group-user-search-item-template') ?? document.createElement('div');
            const imgEl = item.querySelector('.js-avatar') as HTMLImageElement | null;
            const emailEl = item.querySelector('.js-email') as HTMLElement | null;

            if (imgEl) {
                const defaultAvatar = imgEl.dataset.defaultAvatar || imgEl.src;
                const avatarUrl = user.avatar_url ? (user.avatar_url.startsWith('http') ? user.avatar_url : `/storage/${user.avatar_url}`) : defaultAvatar;
                imgEl.src = avatarUrl;
                imgEl.onerror = () => {
                    imgEl.src = defaultAvatar;
                };
            }

            if (emailEl) {
                emailEl.textContent = user.email;
            } else {
                item.textContent = user.email;
            }

            item.addEventListener('click', () => { addMemberToGroup(user); item.remove(); });
            modalList.appendChild(item);
        });
    } catch (e) { console.error(e); }
}

function addMemberToGroup(user: any, canDelete: boolean = true) {
    const container = document.getElementById('addedMembers');
    if (!container || selectedUsers.some(u => u.id === user.id)) return;

    const role = user.role ?? 4;
    selectedUsers.push({ id: user.id, role });

    const card = cloneTemplate('group-member-item-template');
    if (!card) {
        return;
    }

    const emailEl = card.querySelector('.js-email') as HTMLElement | null;
    const roleSelect = card.querySelector('.role-select') as HTMLSelectElement | null;
    const actionsEl = card.querySelector('.js-actions') as HTMLElement | null;

    if (emailEl) {
        emailEl.textContent = user.email;
    }
    if (roleSelect) {
        roleSelect.value = role.toString();
    }
    if (!canDelete && actionsEl) {
        actionsEl.classList.add('d-none');
    }

    if (canDelete) {
        roleSelect?.addEventListener('change', (e) => {
            const target = selectedUsers.find(u => u.id === user.id);
            if (target) target.role = parseInt((e.target as HTMLSelectElement).value);
        });
        card.querySelector('.remove-user')?.addEventListener('click', () => {
            selectedUsers = selectedUsers.filter(u => u.id !== user.id);
            card.remove();
        });
    }
    container.appendChild(card);
}

async function saveGroupData() {
    const title = (document.getElementById('titleInput') as HTMLInputElement).value.trim();
    if (!title) return (document.getElementById('errorHandler')!.innerHTML = "Title required");

    const formData = new FormData();
    formData.append('name', title);
    formData.append('description', (document.getElementById('descriptionInput') as HTMLTextAreaElement).value.trim());

    let userIds: number[] = [];
    const rolesMap: Record<string, number> = {};
    selectedUsers.forEach(u => {
        userIds.push(u.id);
        rolesMap[u.id.toString()] = u.role;
    });

    const imgInput = document.getElementById('event-image-upload') as HTMLInputElement;
    if (imgInput?.files?.[0]) formData.append('img', imgInput.files[0]);
    if (deletePictureFlag) formData.append('delete_picture', 'true');

    const saveBtn = document.getElementById('saveGroup') as HTMLButtonElement;
    saveBtn.disabled = true;
    saveBtn.innerText = "Saving...";

    if (currentGroupId) {
        try {
            formData.append('_method', 'PATCH');

            const toAdd = userIds.filter(id => !initialUserIds.includes(id));
            const toDelete = initialUserIds.filter(id => !userIds.includes(id));
            await api.post(`/api/group/${currentGroupId}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            if (Object.keys(rolesMap).length > 0) {await api.patch(`/api/group/${currentGroupId}/members`, { users_roles: rolesMap });}
            await addMembers(toAdd, rolesMap);
            await deleteMembers(toDelete);
            window.location.reload();
        } catch (error: any) {
            console.error(error.response?.data || error);
            const errHandler = document.getElementById('errorHandler');
            if (errHandler) errHandler.innerHTML = "Error updating group.";
            saveBtn.disabled = false;
            saveBtn.innerText = "Update Group";
        }
    } else {
        try {
            const res = await api.post(`/api/group`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            const newGroupId = res.data.data.id;

            if (userIds.length > 0) {
                await api.post(`/api/group/${newGroupId}/members`, {
                    users_ids: userIds,
                    users_roles: rolesMap
                });
            }

            window.location.reload();
        } catch (e: any) {
            console.error(e.response?.data || e);
            const errHandler = document.getElementById('errorHandler');
            if (errHandler) errHandler.innerHTML = "Error creating group.";
            saveBtn.disabled = false;
            saveBtn.innerText = "Save Group";
        }
    }
}

async function deleteMembers(toDelete: number[]) {
    if (toDelete.length < 1) return;

    await api.delete(`/api/group/${currentGroupId}/members`, {
        data: { users_ids: toDelete }
    });
}

async function addMembers(toAdd: number[], rolesMap: Record<number, number>) {
    if (toAdd.length > 0) {
        const newMembersRoles: Record<number, number> = {};
        toAdd.forEach(id => {
            if (rolesMap[id] !== undefined) {
                newMembersRoles[id] = rolesMap[id];
            }
        });

        await api.post(`/api/group/${currentGroupId}/members`, {
            users_ids: toAdd,
            users_roles: newMembersRoles
        });
    }
}

async function deleteGroupData() {
    if (!currentGroupId || !confirm("Are you sure?")) return;
    try { await api.delete(`/api/group/${currentGroupId}`); window.location.reload(); }
    catch (e) { document.getElementById('errorHandler')!.innerHTML = "Error deleting."; }
}

function checkUrlForOpenGroup() {
    const openId = new URLSearchParams(window.location.search).get('open');
    if (openId) {
        setTimeout(() => {
            const targetCard = document.querySelector(`.group-card[data-id="${openId}"]`) as HTMLElement;
            if (targetCard) { targetCard.click(); window.history.replaceState({}, document.title, window.location.pathname); }
        }, 500);
    }
}
