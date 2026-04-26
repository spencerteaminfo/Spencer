import api from '../bootstrap';

const cloneTemplate = (id: string): HTMLElement | null => {
    const template = document.getElementById(id) as HTMLTemplateElement | null;
    if (!template) {
        return null;
    }

    return template.content.firstElementChild?.cloneNode(true) as HTMLElement | null;
};

document.addEventListener("DOMContentLoaded", async()=>{
    const addedMembersContainer = document.getElementById("userBulletList") as HTMLDivElement;
    const groupId: string | undefined = addedMembersContainer.dataset.groupId;
    const notIterestedContainer = document.getElementById("not-interested-container");
    const iterestedContainer = document.getElementById("interested-container");
    const TrueAttendece = document.getElementById("interested");
    const FalseAttendence = document.getElementById("not-interested");
    const event = TrueAttendece?.dataset.eventid;
    const currentUserId = document.querySelector('meta[name="current-user-id"]')?.getAttribute('content');
    const defaultAvatar = document.body.dataset.defaultAvatar || '';
    const members: any[] = [];
    const attendanceData: any[] = [];
    console.log(currentUserId);

    if (groupId && addedMembersContainer) {
        const card = cloneTemplate('event-detail-group-card-template');
        if (card) {
            addedMembersContainer.appendChild(card);
        }
    }
    members.forEach((member:any) => {
        const member_id = member.pivot.user_id;
        const currentAttendance = attendanceData.find((a: any) => a.membership_id == member_id);
        const attends = currentAttendance?.attends || false;
        const attendanceBox = attends ? iterestedContainer:  notIterestedContainer;
        const notIterestedDiv = cloneTemplate('event-detail-attendance-item-template');
        if (!notIterestedDiv) {
            return;
        }

        notIterestedDiv.id = "member-"+member_id;
        const imgEl = notIterestedDiv.querySelector('.js-avatar') as HTMLImageElement | null;
        const emailEl = notIterestedDiv.querySelector('.js-email') as HTMLElement | null;
        if (imgEl) {
            const avatarUrl = member.avatar_url || member.avatarUrl || '';
            if (typeof avatarUrl === 'string' && avatarUrl.length > 0) {
                const isAbsolute = avatarUrl.startsWith('http://') || avatarUrl.startsWith('https://');
                const isStorageAbsolute = avatarUrl.startsWith('/storage/');
                imgEl.src = (isAbsolute || isStorageAbsolute) ? avatarUrl : `/storage/${avatarUrl}`;
            } else {
                imgEl.src = defaultAvatar;
            }

            if (defaultAvatar) {
                imgEl.onerror = () => {
                    imgEl.onerror = null;
                    imgEl.src = defaultAvatar;
                };
            }
        }
        if (emailEl) {
            emailEl.textContent = member.email;
        }

        attendanceBox?.append(notIterestedDiv);
    });

    TrueAttendece?.addEventListener("click", async ()=>{
        try{
            await api.patch("/api/event/"+event+"/attendance", {
                user_id: currentUserId,
                attends: true,
            });
            showAttendanceSuccessNotification();
        } catch (e: any){
            console.error("Error: "+e);
        }

    })
    FalseAttendence?.addEventListener("click", async ()=>{
        try{
            await api.patch("/api/event/"+event+"/attendance", {
                user_id: currentUserId,
                attends: false,
            });
            showAttendanceSuccessNotification();
        } catch (e: any){
            console.log("Error: "+e);
        }

    })
})
function showAttendanceSuccessNotification(message: string = 'Attendance byla úspěšně změněna'): void {
    const alertDiv: HTMLDivElement = document.createElement('div');

    alertDiv.className = 'mt-2 alert alert-success alert-dismissible fade show shadow-sm';
    alertDiv.setAttribute('role', 'alert');

    alertDiv.innerHTML = `${message}`;

    const container: HTMLElement | null = document.getElementById('interest-conteiner');

    if (container) {
        container.append(alertDiv);
    }

    setTimeout(() => {
        alertDiv.classList.remove('show');

        setTimeout(() => alertDiv.remove(), 150);
    }, 3000);
}


