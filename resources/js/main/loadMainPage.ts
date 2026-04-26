import api from '../bootstrap';
import '../group/loadGroup';

interface EventData {
    title: string;
    deadline: string;
}

addEventListener("DOMContentLoaded", ()=>{
    showEvents(true);
})

const renderCompactEventCard = (event: any, clockIconPath: string, formattedDate: string): HTMLElement | null => {
    const template = document.getElementById('event-card-compact-template') as HTMLTemplateElement | null;
    if (!template) {
        return null;
    }

    const clone = template.content.cloneNode(true) as DocumentFragment;
    const linkEl = clone.querySelector('.js-event-link') as HTMLAnchorElement | null;
    const titleEl = clone.querySelector('.js-event-title') as HTMLElement | null;
    const deadlineEl = clone.querySelector('.js-event-deadline') as HTMLElement | null;
    const iconEl = clone.querySelector('.js-event-clock-icon') as HTMLImageElement | null;

    if (linkEl) linkEl.href = `/event/${event.id}`;
    if (titleEl) titleEl.textContent = event?.title ?? '';
    if (deadlineEl) deadlineEl.textContent = formattedDate;
    if (iconEl) iconEl.src = clockIconPath;

    return clone.firstElementChild as HTMLElement | null;
};

async function showEvents(pageType:boolean) {
    try{
        await api.get('/sanctum/csrf-cookie');
        const response:any = await api.get('/api/events', {});
        const newestEventsContainer = document?.getElementById("container-events") as HTMLDivElement;
        const clockIconPath = newestEventsContainer?.getAttribute('data-url')|| "" as string;

        if (response.data) {
            const events = [...response.data];
            if (events.length > 5 && pageType === true) {
                events.length = 5;
            }

            newestEventsContainer!.innerHTML = '';
            const fragment = document.createDocumentFragment();

            for (const element of events) {
                const formattedDate = new Date(element?.deadline).toLocaleDateString('cs-CZ');
                const card = renderCompactEventCard(element, clockIconPath, formattedDate);
                if (card) {
                    fragment.appendChild(card);
                }
            }

            newestEventsContainer!.appendChild(fragment);
        } else{
            return '<p class="text-muted">Žádné nadcházející události.</p>';
        }

    } catch(error){
        console.error(error);
    }
}
