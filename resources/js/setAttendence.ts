import api from './bootstrap';
const TrueAttendece = document.getElementById("interested");
const FalseAttendence = document.getElementById("not-interested");
const event = TrueAttendece?.dataset.eventid;
console.log(event); 

TrueAttendece?.addEventListener("click", async ()=>{
    const res = await api.get("/api/event/"+event+"/members");
    console.log(res);
    const response = await api.patch("/api/event/"+event+"/set-attends", {params: {
        user_id: "smth",
        attends: "True",
    }});
})