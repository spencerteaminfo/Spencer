import api from './bootstrap';

import ErrorHandlingForm from './errorHandling';
import type { Group } from './models';
const title = document.getElementById("input-title") as HTMLInputElement;
const description = document.getElementById("input-description") as HTMLTextAreaElement;
const deadline = document.getElementById("input-deadline") as HTMLInputElement;
const from = document.getElementById("input-from") as HTMLInputElement;
const to = document.getElementById("input-to") as HTMLInputElement;
const img = document.getElementById("event-image-upload") as HTMLInputElement;
const submitBtn = document.getElementById("save-changes") as HTMLButtonElement;
const addedMembersContainer = document.getElementById('addedMembers') as HTMLDivElement | null;
const searchInput = document.getElementById("searchInput") as HTMLInputElement | null;

let timeout: ReturnType<typeof setTimeout>;
let selectedGroupsIds: number[] = [];


submitBtn.addEventListener("click", async (e)=>{
    let countError = 0;
    e.preventDefault();
    if (from.value > to.value) {
        console.log("ahoj from je vetsi nez to")
        ErrorHandlingForm(from, "fromNotErrorBlock", "Event nemůže skončit dříve než začne");
        from?.focus();
        countError++;
    }
    if (deadline.value > to.value) {
        console.log("ahoj deadline je vetsi nez to")
        ErrorHandlingForm(deadline, "deadlineBadInputToToErrorBlock", "Deadline nemůže být později než by skončil event");
        deadline?.focus();
        countError++;
    }
    if (deadline.value > from.value) {
        console.log("ahoj deadline je vetsi nez from")
        ErrorHandlingForm(deadline, "deadlineBadInputToFromErrorBlock", "Deadline nemůže být později než by začal Event");
        deadline?.focus();
        countError++;
    }
    if (!title?.value.trim()) {
        ErrorHandlingForm(title, "titleErrorBlock", "title je povinny");
        title?.focus();
        countError++;
    }
    if (!deadline?.value) {
        ErrorHandlingForm(deadline, "deadlineErrorBlock", "deadline je povinny");
        deadline?.focus();
        countError++;
    }
    if (!from?.value) {
        ErrorHandlingForm(from, "fromErrorBlock", "Event musí někdy začít");
        from?.focus();
        countError++;
    }
    if (!to?.value) {
        ErrorHandlingForm(to, "toErrorBlock", "Event musi nekdy koncit");
        to?.focus();
        countError++;
    }
    if (selectedGroupsIds.length < 1) {
        console.log("Musise tam dat alespon jedna groupka")
        countError++;
    }
    if (countError > 0) {
        return;
    }
    const formData = new FormData();
    formData.append("title", title.value.trim());
    formData.append("description",description.value.trim());
    formData.append("deadline", deadline.value);
    formData.append("from", from.value);
    formData.append("to", to.value);
    // Only one group
    if (selectedGroupsIds.length > 0) {
        selectedGroupsIds.forEach(num => {
            formData.append("group_ids[]", num.toString());
        });
    }

    if (img.files && img.files?.[0]) {
        formData.append("img", img.files?.[0]);
    } else{
        formData.append("img", "");
    }
    try{
        submitBtn.disabled=true;
        console.log(formData);
        const response = await api.post("/api/event", formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        window.location.href="/";
    }catch (error:unknown){
        console.error("Error: "+error);
        submitBtn.disabled=false;
    }
})
const modalList = document.getElementById('userBulletList');

const searchAndLog = async (search: string) => {
    try {
        const response = await api.get('/api/groups', {params: {title: search}});
        console.log(response)
        const groups: Group[] = response.data.data;
        console.log(response);
        if (!modalList) return;
        modalList.innerHTML = '';
        const filteredGroups = groups.filter(u => !selectedGroupsIds.includes(u.id));
        if (filteredGroups.length === 0) return;
        const container = document.createElement('div');
        container.className = "d-flex flex-column gap-2 w-100";
        filteredGroups.forEach(user => container.appendChild(createUserCard(user)));
        modalList.appendChild(container);
    } catch (error) {
        console.error(error);
    }
};
const createUserCard = (group: Group) => {
    const groupName = group.name ?? '';
    const card = document.createElement('div');
    card.className = "card border border-light-subtle rounded-pill px-3 py-2 w-100";
    card.innerHTML = `
        <div class="add-user-btn d-flex align-items-center">
            <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                <img src="https://ui-avatars.com/api/?name=${groupName}&background=E9ECEF&color=6C757D" class="w-100 profile-pic" alt="acc">
            </div>
            <div class="small flex-grow-1">
                <span class="text-muted"><strong>${groupName}</strong></span>
            </div>
            <div class="fw-bold text-primary px-2" role="button">+</div>
        </div>`;
    card.querySelector('.add-user-btn')?.addEventListener('click', () => {
        addMemberToGroup(group);
        card.remove();
    });
    return card;
};
const addMemberToGroup = (group: Group, canDelete: boolean = true) => {
    if (!addedMembersContainer) return;
    if (selectedGroupsIds.includes(group.id)) return;

    selectedGroupsIds.push(group.id);

    const card = document.createElement('div');
    card.className = "card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100";
    card.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                <img src="https://ui-avatars.com/api/?name=${group.name}&background=198754&color=fff" class="w-100 profile-pic" alt="acc">
            </div>
            <div class="small">
                <span class="text-muted d-none d-sm-inline">${group.name}</span>
            </div>
            ${canDelete ? '<div class="remove-user-btn text-danger small fw-bold px-1" role="button">✕</div>' : ''}
        </div>`;

    if (canDelete) {
        card.querySelector('.remove-user-btn')?.addEventListener('click', () => {
            selectedGroupsIds = selectedGroupsIds.filter(id => id !== group.id);
            card.remove();
        });
    }

    addedMembersContainer.appendChild(card);
    if (modalList) modalList.innerHTML = '';
    if (searchInput) searchInput.value = '';
};

searchInput?.addEventListener("input", (e) => {
    const val = (e.target as HTMLInputElement).value;
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        if (val.length > 0) searchAndLog(val);
        else if (modalList) modalList.innerHTML = '';
    }, 300);
});

