import api from '../bootstrap';
import type { Group } from '@/models';

document.addEventListener('DOMContentLoaded', loadGroups);

async function loadGroups() {
    const container = document.getElementById('groups-container');
    const template = document.getElementById('group-card-template') as HTMLTemplateElement;
    if (!container || !template) return;

    const currentUserId = parseInt(container.getAttribute('data-user-id') || '0');

    try {
        const response = await api.get('/api/groups');
        const groups: Group[] = response.data.data;

        if (!groups || groups.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted">Zatím žádné skupiny.</div>';
            return;
        }

        container.innerHTML = '';

        groups.forEach(group => {
            const clone = template.content.cloneNode(true) as DocumentFragment;
            const wrapper = clone.querySelector('.js-card-wrapper') as HTMLElement;
            const myMembership = group.users?.find(u => u.id === currentUserId);
            const myRoleId = myMembership?.pivot?.role_id ?? 4;
            const isCreator = myRoleId === 3;

            const roleOwnerEl = clone.querySelector('.js-role-owner');
            const roleCashierEl = clone.querySelector('.js-role-cashier');
            const roleMemberEl = clone.querySelector('.js-role-member');

            if (myRoleId === 3) {
                roleOwnerEl?.classList.remove('d-none');
            } else if (myRoleId === 5) {
                roleCashierEl?.classList.remove('d-none');
            } else {
                roleMemberEl?.classList.remove('d-none');
            }

            clone.querySelector('.js-name')!.textContent = group.name;

            if (group.picture_url) {
                const imgEl = clone.querySelector('.js-img') as HTMLImageElement;
                imgEl.src = group.picture_url.startsWith('http') ? group.picture_url : `/storage/${group.picture_url}`;
                clone.querySelector('.js-img-wrapper')!.classList.remove('d-none');
            }

            const existingMembers = (group.users || []).filter(u => u.id !== currentUserId).map(u => ({
                id: u.id, email: u.email, role: u.pivot?.role_id ?? 4
            }));

            wrapper.dataset.id = group.id.toString();
            wrapper.dataset.name = group.name;
            wrapper.dataset.description = group.description || '';
            wrapper.dataset.isCreator = isCreator.toString();
            wrapper.dataset.members = JSON.stringify(existingMembers);
            wrapper.dataset.picture = group.picture_url || '';

            container.appendChild(clone);
        });

    } catch (e) {
        console.error(e);
        container.innerHTML = '<div class="col-12 text-center text-danger">Nepodařilo se načíst skupiny.</div>';
    }
}