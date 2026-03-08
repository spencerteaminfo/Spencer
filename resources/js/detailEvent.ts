import api from './bootstrap';
const addedMembersContainer = document.getElementById("userBulletList") as HTMLDivElement;
let nazev:string = ""
const searchGroup = async () =>{
    const description: string | undefined = addedMembersContainer.dataset.groupid;
    const data = await api.get('/api/groups', {params: {id: description}});
    const groupsId=data.data.data;
    groupsId.forEach((element:any) => {
        if(element.id == description) {
            nazev += element.name
        }
    });
    const card = document.createElement('div');
    card.className = "card border border-light-subtle rounded-pill px-3 py-2 mb-1 w-100";
    card.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="rounded-circle overflow-hidden border border-secondary-subtle me-2">
                <img src="https://ui-avatars.com/api/?name=${nazev}&background=198754&color=fff" class="w-100 profile-pic" alt="acc">
            </div>
            <div class="small">
                <span class="text-muted d-none d-sm-inline">${nazev}</span>
            </div>
        </div>`;
    addedMembersContainer.appendChild(card);

    console.log(nazev);
}
if (addedMembersContainer) {
    searchGroup();
}