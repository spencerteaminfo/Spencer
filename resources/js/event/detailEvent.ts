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
    interface EventPayment {
        id: number;
        event_id: number;
        user_id: number;
        amount_paid: number;
        created_at?: string; // volitelné
        updated_at?: string; // volitelné
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
    const currentEventId = document.querySelector('meta[name="current-event-id"]')?.getAttribute('content');
    const allUsersId = document.querySelector('meta[name="all-user-ids"]')?.getAttribute('content');
    const groupIdsRaw = document.querySelector('meta[name="data-groups-ids"]')?.getAttribute('content');

    const groupIds: number[] = groupIdsRaw ? JSON.parse(groupIdsRaw) : [];
    const membersIds: number[] = allUsersId ? JSON.parse(allUsersId) : [];
    console.log(membersIds)
    const values = Object.values(membersIds);
    const allIds = values.flat().map(user => user.id);

    console.log(allIds); 
    const attendanceData: any[] = [];
    console.log(groupIds)
    const paymentsInfo = await api.get(`/api/event/${currentEventId}/payments`);
    const payments = paymentsInfo.data.data as EventPayment[];
    console.log(paymentsInfo)
    for (const userId of allIds){
        const span = document.getElementById(`span${userId}`)
        const button = document.getElementById(`buttton${userId}`)
        const userPayment = payments.find(p => p.user_id === userId);
        const numberOfPaid = userPayment!.amount_paid.toString();
        if (span) {
        span!.innerHTML = numberOfPaid
        }
        button?.setAttribute('data-amount', numberOfPaid)
        console.log(numberOfPaid)
        console.log(userId)
    }

    const loggedInUserId = parseInt(currentUserId || '0', 10);
    let admin = false;
    for (const id of groupIds) {
        const response = await api.get(`/api/group/${id}/members`);
        const groupMembers = response.data.data;
        const currentUserData = groupMembers.find((m: User) => m.id === loggedInUserId);
        console.log(currentUserData);
        if (currentUserData && isAuthorizedStaff(currentUserData)) {
            admin = true;
        }
    }
    if (admin){
        console.error("ahoj")
        const paymentModalEl = document.getElementById('paymentModal');
        const paymentModal = paymentModalEl ? new bootstrap.Modal(paymentModalEl) : null;

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

        document.getElementById('modal-save-btn')?.addEventListener('click', async () => {
            const userId = (document.getElementById('modal-user-id') as HTMLInputElement).value;
            const amount = (document.getElementById('modal-amount-input') as HTMLInputElement).value;
            const saveBtn = document.getElementById('modal-save-btn') as HTMLButtonElement;

            try {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Ukládám...';

                await api.patch(`/api/event/${currentEventId}/payment`, {
                    user_id: currentUserId,
                    amount: amount
                });

                const rowBtn = document.querySelector(`.admin-only-info[data-user-id="${userId}"] .open-payment-modal`);
                if (rowBtn) {
                    (rowBtn as HTMLElement).dataset.amount = amount;
                    rowBtn.querySelector('span')!.innerText = `${amount}`;
                }

                paymentModal?.hide();
                showAttendanceSuccessNotification('Platba byla úspěšně uložena');

            } catch (err) {
                console.error(err);
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Uložit platbu';
            }
        });
        document.getElementById('modal-correct-btn')?.addEventListener('click', async () => {
            const userId = (document.getElementById('modal-user-id') as HTMLInputElement).value;
            const amount = (document.getElementById('modal-amount-input') as HTMLInputElement).value;
            const saveBtn = document.getElementById('modal-correct-btn') as HTMLButtonElement;

            try {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Ukládám...';

                await api.patch(`/api/event/${currentEventId}/set-paid`, {
                    user_id: currentUserId,
                });

                const rowBtn = document.querySelector(`.admin-only-info[data-user-id="${userId}"] .open-payment-modal`);
                

                paymentModal?.hide();
                showAttendanceSuccessNotification('Platba byla úspěšně uložena');

            } catch (err) {
                console.error(err);
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Uložit platbu';
            }
        });
    }
    

    

    if (groupId && addedMembersContainer) {
        const card = cloneTemplate('event-detail-group-card-template');
        if (card) {
            addedMembersContainer.appendChild(card);
        }
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


