import api from './bootstrap'; 

document.addEventListener("DOMContentLoaded", async()=>{
    const addedMembersContainer = document.getElementById("userBulletList") as HTMLDivElement;
    const groupId: string | undefined = addedMembersContainer.dataset.groupid;
    const notIterestedContainer = document.getElementById("not-interested-container");
    const iterestedContainer = document.getElementById("interested-container");
    let nazev:string = ""
    let user_id:string = "";
    const TrueAttendece = document.getElementById("interested");
    const FalseAttendence = document.getElementById("not-interested");
    const event = TrueAttendece?.dataset.eventid;
    const membersGroup = await api.get("/api/group/"+groupId+"/members"); 
    const members = membersGroup.data.data;
    const groupRes = await api.get("/api/groups", { params: { title: "" } });
    const groups = groupRes.data.data;
    console.log(groups)
    const currentGroup = groups.find((g: any) => g.id == groupId);
    const currentUserId = document.querySelector('meta[name="current-user-id"]')?.getAttribute('content');
    const attendance = await api.get('/api/event/'+event+'/attendance');
    const attendanceData = attendance.data.data
    console.log(attendance);

    if (currentGroup && addedMembersContainer) {
        const card = document.createElement('div');
        card.className = "card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100";
        card.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                    <img src="https://ui-avatars.com/api/?name=${currentGroup.name}&background=198754&color=fff" class="w-100 profile-pic" alt="acc">
                </div>
                <div class="small">
                    <span class="text-muted d-none d-sm-inline">${currentGroup.name}</span>
                </div>
            </div>`;
        addedMembersContainer.appendChild(card);
    }
    members.forEach((member:any) => {
        const member_id = member.pivot.user_id;
        const currentAttendance = attendanceData.find((a: any) => a.membership_id == member_id);
        const attends = currentAttendance.attends;
        console.log(attends);
        const attendanceBox = attends ? iterestedContainer:  notIterestedContainer;
        const notIterestedDiv = document.createElement('div');
        notIterestedDiv.className = "d-flex align-items-center mb-3";
        notIterestedDiv.innerHTML = `
            <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2 flex-shrink-0" style="width: 24px; height: 24px;">
                <img src="https://ui-avatars.com/api/?name=${member.email}&background=198754&color=fff" class="w-100" alt="user">
            </div>
            <span class="small fw-medium">${member.email}</span>`;
        
        attendanceBox?.append(notIterestedDiv);
    });
    
    TrueAttendece?.addEventListener("click", async ()=>{
        const response = await api.patch("/api/event/"+event+"/attendance", {
            user_id: currentUserId,
            attends: true,
        });
        console.log(response)
    })
    FalseAttendence?.addEventListener("click", async ()=>{
        const response = await api.patch("/api/event/"+event+"/attendance", {
            user_id: currentUserId,
            attends: false,
        });
        console.log(response)
    })
})


