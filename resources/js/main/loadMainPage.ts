import api from '../bootstrap';
import '../group/loadGroup';

interface EventData {
    title: string;
    deadline: string;
}

addEventListener("DOMContentLoaded", ()=>{
    showEvents(true);
})
async function showEvents(pageType:boolean) {
    try{
        await api.get('/sanctum/csrf-cookie');
        const response:any = await api.get('/api/events', {});
        const newestEventsContainer = document?.getElementById("container-events") as HTMLDivElement;
        const clockIconPath = newestEventsContainer?.getAttribute('data-url')|| "" as string;

        if (response.data) {
            const renderEvents = (timeClock:string): string => {
                let neco = "";
                if (response.data.length > 5 && pageType == true) {
                    response.data.length = 5;
                }
                for (let index = 0; index < response.data.length; index++) {
                    const formattedDate = new Date(response?.data[index]?.deadline).toLocaleDateString('cs-CZ');
                    const element = response.data[index];
                    neco += `<a href="/event/${element.id}" class="text-decoration-none">
                                <div class="col-12 col-md-12 mb-3">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                        <div class="card-header bg-white border-0 py-3 px-3">
                                            <h5 class="mb-0 text-dark fw-bold text-truncate">${element?.title}</h5>
                                        </div>
                                        <div class="card-footer bg-white border-0 py-3 px-3 mt-auto">
                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                <img src="${clockIconPath}" alt="time" class="h-auto w-auto opacity-75">
                                                <span>Deadline: ${formattedDate}</span>
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
            return '<p class="text-muted">Žádné nadcházející události.</p>';
        }

    } catch(error){
        console.error(error);
    }
}
