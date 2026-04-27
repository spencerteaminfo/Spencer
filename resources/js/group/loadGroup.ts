import api from '../bootstrap';

document.addEventListener('DOMContentLoaded', loadSimpleGroups);

function renderMessage(container: HTMLElement, text: string, cssClass: string): void {
    container.innerHTML = '';
    const messageEl = document.createElement('p');
    messageEl.className = cssClass;
    messageEl.textContent = text;
    container.appendChild(messageEl);
}

async function loadSimpleGroups() {
    const container = document.getElementById('container-groups') as HTMLElement | null;
    const template = document.getElementById('group-simple-template') as HTMLTemplateElement | null;

    if (!container || !template) return;

    const iconPath = container.dataset.url || '';
    const emptyText = container.dataset.emptyText || 'home.group.empty';
    const errorText = container.dataset.errorText || 'home.group.load_failed';

    try {
        const response: any = await api.get('/api/groups');
        const groups = (response.data?.data || []).slice(0, 5);

        if (groups.length === 0) {
            renderMessage(container, emptyText, 'text-muted');
            return;
        }

        container.innerHTML = '';

        for (const group of groups) {
            const clone = template.content.cloneNode(true) as DocumentFragment;
            const linkEl = clone.querySelector('.js-link') as HTMLAnchorElement | null;
            const nameEl = clone.querySelector('.js-name') as HTMLElement | null;
            const descEl = clone.querySelector('.js-desc') as HTMLElement | null;
            const membersCountEl = clone.querySelector('.js-members-count') as HTMLElement | null;
            const iconEl = clone.querySelector('.js-icon') as HTMLImageElement | null;

            if (!linkEl || !nameEl || !descEl || !membersCountEl || !iconEl) {
                continue;
            }

            const fallbackDescription = descEl.dataset.fallback || 'home.group.no_description';
            const usersCount = Array.isArray(group.users) ? group.users.length : 0;

            linkEl.href = `/groups?open=${group.id}`;
            nameEl.textContent = group.name;
            descEl.textContent = (group.description || '').trim() || fallbackDescription;
            membersCountEl.textContent = String(usersCount);
            iconEl.src = iconPath;

            if (group.picture_url) {
                const imgEl = clone.querySelector('.js-img') as HTMLImageElement | null;
                if (imgEl) {
                    imgEl.src = group.picture_url.startsWith('http') ? group.picture_url : `/storage/${group.picture_url}`;
                    clone.querySelector('.js-img-wrapper')?.classList.remove('d-none');
                }
            }

            container.appendChild(clone);
        }
    } catch (error) {
        console.error('Error while loading groups for homepage:', error);
        renderMessage(container, errorText, 'text-danger small');
    }
}
