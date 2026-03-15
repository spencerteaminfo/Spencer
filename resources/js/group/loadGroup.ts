import api from '../bootstrap';

document.addEventListener('DOMContentLoaded', loadSimpleGroups);

async function loadSimpleGroups() {
    const container = document.getElementById('container-groups');
    const template = document.getElementById('group-simple-template') as HTMLTemplateElement;

    if (!container || !template) return;

    const iconPath = container.getAttribute('data-url') || '';

    try {
        const response: any = await api.get('/api/groups');
        let groups = response.data.data;

        if (groups.length > 5) {
            groups.length = 5;
        }

        if (!groups || groups.length === 0) {
            container.innerHTML = '<p class="text-muted">Žádné skupiny.</p>';
            return;
        }

        container.innerHTML = '';

        for (let index = 0; index < groups.length; index++) {
            const group = groups[index];
            const clone = template.content.cloneNode(true) as DocumentFragment;
            const linkEl = clone.querySelector('.js-link') as HTMLAnchorElement;
            const nameEl = clone.querySelector('.js-name')!;
            const descEl = clone.querySelector('.js-desc')!;
            const membersEl = clone.querySelector('.js-members')!;
            const iconEl = clone.querySelector('.js-icon') as HTMLImageElement;
            linkEl.href = `/groups?open=${group.id}`;

            nameEl.textContent = group.name;
            descEl.textContent = group.description || 'Bez popisu';
            membersEl.textContent = `Skupina (Členů: ${group.users ? group.users.length : 0})`;
            iconEl.src = iconPath;
            if (group.picture_url) {
                const imgEl = clone.querySelector('.js-img') as HTMLImageElement;
                imgEl.src = group.picture_url.startsWith('http') ? group.picture_url : `/storage/${group.picture_url}`;
                clone.querySelector('.js-img-wrapper')?.classList.remove('d-none');
            }
            container.appendChild(clone);
        }

    } catch (error) {console.error("Chyba při načítání skupin pro index:", error);
        container.innerHTML = '<div class="text-danger small">Nepodařilo se načíst skupiny.</div>';
    }
}
