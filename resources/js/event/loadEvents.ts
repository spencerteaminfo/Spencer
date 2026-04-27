import api from '../bootstrap';

interface EventData {
    title: string;
    description: string;
    deadline: string;
    starts_at: string;
    ends_at: string;
}
addEventListener("DOMContentLoaded", ()=>{
    showEvents(false);
})

const renderEventCard = (
    event: any,
    clockIconPath: string,
    formattedDeadline: string,
    formattedStart: string,
    formattedEnd: string
): HTMLElement | null => {
    const template = document.getElementById('event-card-full-template') as HTMLTemplateElement | null;
    if (!template) {
        return null;
    }

    const clone = template.content.cloneNode(true) as DocumentFragment;
    const linkEl = clone.querySelector('.js-event-link') as HTMLAnchorElement | null;
    const titleEl = clone.querySelector('.js-event-title') as HTMLElement | null;
    const descEl = clone.querySelector('.js-event-description') as HTMLElement | null;
    const deadlineEl = clone.querySelector('.js-event-deadline') as HTMLElement | null;
    const startEl = clone.querySelector('.js-event-start') as HTMLElement | null;
    const endEl = clone.querySelector('.js-event-end') as HTMLElement | null;
    const iconEl = clone.querySelector('.js-event-clock-icon') as HTMLImageElement | null;

    if (linkEl) linkEl.href = `/event/${event.id}`;
    if (titleEl) titleEl.textContent = event?.title ?? '';
    if (descEl) descEl.textContent = event?.description ?? '';
    if (deadlineEl) deadlineEl.textContent = formattedDeadline;
    if (startEl) startEl.textContent = formattedStart;
    if (endEl) endEl.textContent = formattedEnd;
    if (iconEl) iconEl.src = clockIconPath;

    return clone.firstElementChild as HTMLElement | null;
};

async function showEvents(pageType:boolean) {
    try{
        const response:any = await api.get('/api/events', {});
        console.log(response);
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
                const formattedDeadline = new Date(element?.deadline).toLocaleDateString('cs-CZ');
                const formattedStart = new Date(element?.starts_at).toLocaleDateString('cs-CZ');
                const formattedEnd = new Date(element?.ends_at).toLocaleDateString('cs-CZ');
                const card = renderEventCard(element, clockIconPath, formattedDeadline, formattedStart, formattedEnd);
                if (card) {
                    fragment.appendChild(card);
                }
            }

            newestEventsContainer!.appendChild(fragment);
        } else{
            '<p class="text-muted">Žádné nadcházející události.</p>';
        }

    } catch(error){
        console.error(error);
    }
}
