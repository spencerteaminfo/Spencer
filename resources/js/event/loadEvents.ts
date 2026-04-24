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
async function showEvents(pageType:boolean) {
    try{
        const response:any = await api.get('/api/events', {});
        console.log(response);
        const newestEventsContainer = document?.getElementById("container-events") as HTMLDivElement;
        const clockIconPath = newestEventsContainer?.getAttribute('data-url')|| "" as string;

        if (response.data) {
            const renderEvents = (timeClock:string): string => {
                let neco = "";
                if (response.data.length > 5 && pageType == true) {
                    response.data.length = 5;
                }
                for (let index = 0; index < response.data.length; index++) {
                    const formattedDeadline = new Date(response?.data[index]?.deadline).toLocaleDateString('cs-CZ');
                    const formattedStart = new Date(response?.data[index]?.starts_at).toLocaleDateString('cs-CZ');
                    const formattedEnd = new Date(response?.data[index]?.ends_at).toLocaleDateString('cs-CZ');
                    const element = response.data[index];
                    neco += `
                            <a href="/event/${element.id}" class="text-decoration-none">
                                <div class="col-12 col-md-12 mb-3">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-white border-0 py-3 px-3">
                                            <h5 class="mb-0 text-dark fw-bold text-truncate">${element?.title}</h5>
                                        </div>
                                        <div class="text-muted mt-1 px-3">
                                            <p>${element?.description}</p>
                                        </div>
                                        <div class="card-footer bg-white border-0 py-3 px-3 mt-auto">
                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                <img src="${clockIconPath}" alt="time" class="h-auto w-auto opacity-75">
                                                <span>Deadline: ${formattedDeadline}</span>
                                                <span>Start: ${formattedStart}</span>
                                                <span>End: ${formattedEnd}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>`

                }
                return neco;
            };
            const Events = renderEvents(clockIconPath);
            newestEventsContainer!.innerHTML = Events
        } else{
            '<p class="text-muted">Žádné nadcházející události.</p>';
        }

    } catch(error){
        console.error(error);
    }
}
