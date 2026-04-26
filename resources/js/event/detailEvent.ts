import api from '../bootstrap';
import * as bootstrap from 'bootstrap';
interface User {
    id: number;
    email: string;
    first_name: string | null;
    last_name: string | null;
    pivot: {
        role_id: number;
        group_id: number;
        user_id: number;
    };
}
enum RoleType {
    MEMBER = 5,
    CASHIER = 4,
    OWNER = 3
}

// Funkce, která nám řekne, jestli je daný uživatel Admin nebo Cashier
const isAuthorizedStaff = (user: User): boolean => {
    if (!user.pivot) return false;
    return user.pivot.role_id === RoleType.CASHIER || user.pivot.role_id === RoleType.OWNER;
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
    const groupIdsRaw = document.querySelector('meta[name="data-groups-ids"]')?.getAttribute('content');

    const groupIds: number[] = groupIdsRaw ? JSON.parse(groupIdsRaw) : [];
    const attendanceData: any[] = [];
    const a = await api.get("/api/group/9/members");
    console.log(groupIds)

    const members: User[] = a.data.data; 
    const loggedInUserId = parseInt(currentUserId || '0', 10);
    let admin = false;
    for (const id of groupIds) {
        const response = await api.get(`/api/group/${id}/members`);
        const groupMembers = response.data.data;
        const currentUserData = members.find((m: User) => m.id === loggedInUserId);
        console.log(currentUserData);
        if (currentUserData && isAuthorizedStaff(currentUserData)) {
            admin = true;
        }
    }
    if (admin){
        console.error("ahoj")
        const paymentModalEl = document.getElementById('paymentModal');
        const paymentModal = paymentModalEl ? new bootstrap.Modal(paymentModalEl) : null;

        // Otevírání modalu a předání dat
        document.querySelectorAll('.open-payment-modal').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.currentTarget as HTMLButtonElement;
                const userId = target.closest('.admin-only-info')?.getAttribute('data-user-id');
                const email = target.dataset.email;
                const amount = target.dataset.amount;
                const avatar = target.dataset.avatar;

                // Naplnění modalu daty
                (document.getElementById('modal-user-id') as HTMLInputElement).value = userId || '';
                (document.getElementById('modal-amount-input') as HTMLInputElement).value = amount || '0';
                (document.getElementById('modal-user-email') as HTMLElement).innerText = email || '';
                (document.getElementById('modal-user-avatar') as HTMLImageElement).src = avatar || '';

                paymentModal?.show();
            });
        });

        // Uložení dat z modalu
        document.getElementById('modal-save-btn')?.addEventListener('click', async () => {
            const userId = (document.getElementById('modal-user-id') as HTMLInputElement).value;
            const amount = (document.getElementById('modal-amount-input') as HTMLInputElement).value;
            const eventId = (document.getElementById('interested') as HTMLElement).dataset.eventid;
            const saveBtn = document.getElementById('modal-save-btn') as HTMLButtonElement;

            try {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Ukládám...';

                await api.patch(`/api/event/${eventId}/update-payment`, {
                    user_id: userId,
                    amount_paid: amount
                });

                // Aktualizace UI v seznamu bez reloadu
                const rowBtn = document.querySelector(`.admin-only-info[data-user-id="${userId}"] .open-payment-modal`);
                if (rowBtn) {
                    (rowBtn as HTMLElement).dataset.amount = amount;
                    rowBtn.querySelector('span')!.innerText = `${amount} Kč`;
                }

                paymentModal?.hide();
                showAttendanceSuccessNotification('Platba byla úspěšně uložena');

            } catch (err) {
                console.error(err);
                alert('Chyba při ukládání');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Uložit platbu';
            }
        });
    }
    

    

    if (groupId && addedMembersContainer) {
        const card = document.createElement('div');
        card.className = "card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100";
        card.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                    <img src="https://ui-avatars.com/api/?name=Group&background=198754&color=fff" class="w-100 profile-pic" alt="acc">
                </div>
                <div class="small">
                    <span class="text-muted d-none d-sm-inline">Group</span>
                </div>
            </div>`;
        addedMembersContainer.appendChild(card);
    }
    

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


