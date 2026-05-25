import api from '../bootstrap';

import ErrorHandlingForm from '../utils/errorHandling';
import type { Group } from '../models';
const title = document.getElementById("input-title") as HTMLInputElement;
const description = document.getElementById("input-description") as HTMLTextAreaElement;
const deadline = document.getElementById("input-deadline") as HTMLInputElement;
const from = document.getElementById("input-from") as HTMLInputElement;
const to = document.getElementById("input-to") as HTMLInputElement;
const price = document.getElementById("input-price") as HTMLInputElement;
const img = document.getElementById("event-image-upload") as HTMLInputElement;
const submitBtn = document.getElementById("save-changes") as HTMLButtonElement;
const addedMembersContainer = document.getElementById('addedMembers') as HTMLDivElement | null;
const searchInput = document.getElementById("searchInput") as HTMLInputElement | null;
const groupSelectionError = document.getElementById('groupSelectionError') as HTMLDivElement | null;
const groupSelectionErrorMessage = groupSelectionError?.dataset.message ?? 'Please add at least one group';

let timeout: ReturnType<typeof setTimeout>;
let selectedGroupsIds: number[] = [];

const setGroupSelectionError = (message: string | null) => {
    if (!groupSelectionError) {
        return;
    }

    if (message) {
        groupSelectionError.textContent = message;
        groupSelectionError.classList.remove('d-none');
    } else {
        groupSelectionError.textContent = '';
        groupSelectionError.classList.add('d-none');
    }
};

const cloneTemplate = (id: string): HTMLElement | null => {
    const template = document.getElementById(id) as HTMLTemplateElement | null;
    if (!template) {
        return null;
    }

    return template.content.firstElementChild?.cloneNode(true) as HTMLElement | null;
};


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
    if (!price?.value) {
        ErrorHandlingForm(to, "priceErrorBlock", "Cena je povinná");
        price?.focus();
        countError++;
    }
    if (selectedGroupsIds.length < 1) {
        setGroupSelectionError(groupSelectionErrorMessage);
        countError++;
    }
    if (countError > 0) {
        return;
    }
    setGroupSelectionError(null);
    const formData = new FormData();
    formData.append("title", title.value.trim());
    formData.append("description",description.value.trim());
    formData.append("price_ammount",price.value.trim());
    formData.append("price_currency", "CZK");
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

    // TODO only a placeholder
    formData.append("price_amount", "100");
    formData.append("price_currency", "CZK");

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
    const card = cloneTemplate('event-group-search-item-template');
    if (!card) {
        const fallback = document.createElement('div');
        fallback.textContent = groupName;
        return fallback;
    }

    const addBtn = (card.querySelector('.add-user-btn') as HTMLElement | null)
        || (card.classList.contains('add-user-btn') ? card : null);
    const plusBtn = card.querySelector('.js-add-group') as HTMLButtonElement | null;
    const imgEl = card.querySelector('.js-avatar') as HTMLImageElement | null;
    const nameEl = card.querySelector('.js-name') as HTMLElement | null;

    if (imgEl) {
        const defaultAvatar = imgEl.dataset.defaultAvatar || imgEl.src;
        const pictureUrl = group.picture_url ? (group.picture_url.startsWith('http') ? group.picture_url : `/storage/${group.picture_url}`) : defaultAvatar;
        imgEl.src = pictureUrl;
        imgEl.onerror = () => {
            imgEl.src = defaultAvatar;
        };
    }
    if (nameEl) {
        nameEl.textContent = groupName;
    }

    const clickTarget = addBtn || card;
    clickTarget.addEventListener('click', () => {
        addMemberToGroup(group);
        card.remove();
    });

    plusBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        addMemberToGroup(group);
        card.remove();
    });

    return card;
};
const addMemberToGroup = (group: Group, canDelete: boolean = true) => {
    if (!addedMembersContainer) return;
    if (selectedGroupsIds.includes(group.id)) return;

    selectedGroupsIds.push(group.id);

    const card = cloneTemplate('event-selected-group-item-template');
    if (!card) {
        return;
    }

    const imgEl = card.querySelector('.js-avatar') as HTMLImageElement | null;
    const nameEl = card.querySelector('.js-name') as HTMLElement | null;
    const removeBtn = card.querySelector('.remove-user-btn') as HTMLElement | null;

    if (imgEl) {
        const defaultAvatar = imgEl.dataset.defaultAvatar || imgEl.src;
        const pictureUrl = group.picture_url
            ? (group.picture_url.startsWith('http') ? group.picture_url : `/storage/${group.picture_url}`)
            : defaultAvatar;
        imgEl.src = pictureUrl;
        imgEl.onerror = () => {
            imgEl.src = defaultAvatar;
        };
    }
    if (nameEl) {
        nameEl.textContent = group.name;
    }
    if (!canDelete && removeBtn) {
        removeBtn.classList.add('d-none');
    }

    if (canDelete) {
        removeBtn?.addEventListener('click', () => {
            selectedGroupsIds = selectedGroupsIds.filter(id => id !== group.id);
            card.remove();
        });
    }

    addedMembersContainer.appendChild(card);
    setGroupSelectionError(null);
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
