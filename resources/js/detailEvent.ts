import api from './bootstrap'; 

document.addEventListener("DOMContentLoaded", async()=>{
    await api.get('/sanctum/csrf-cookie');
    const addedMembersContainer = document.getElementById("userBulletList") as HTMLDivElement;
    const groupId: string | undefined = addedMembersContainer.dataset.groupid;
    let nazev:string = ""
    let user_id:string = "";
    const TrueAttendece = document.getElementById("interested");
    const FalseAttendence = document.getElementById("not-interested");
    const event = TrueAttendece?.dataset.eventid;
    const res = await api.get("/api/group/"+groupId+"/members"); 
    const members = res.data.data;
    const groupRes = await api.get("/api/groups", { params: { title: "" } });
    const groups = groupRes.data.data;
    const currentGroup = groups.find((g: any) => g.id == groupId);

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
        if(member.email == groupId) {
            console.log(member.name)
            console.log(member.pivot.user_id)
        }
    });
    console.log(members);
    TrueAttendece?.addEventListener("click", async ()=>{
    const response = await api.patch("/api/event/"+event+"/set-attends", {params: {
        user_id: "smth",
        attends: "True",
    }});
})
})


