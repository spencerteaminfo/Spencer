const sidebarToggle = localStorage.getItem("sidebarCollapse");
    if (sidebarToggle === "true") {
        document.getElementById("main-sidebar")?.classList.toggle("collapsed");
        document.getElementById('event-submenu')?.classList.toggle("ps-4");
    }
const eventSubmenu = localStorage.getItem("EventSubmenuToggle");
if (eventSubmenu === "true" && document.getElementById("event-chevron")) {
    document.getElementById("event-submenu")?.classList.remove("d-none");
    document.getElementById("event-chevron")!.style.transform = "rotate(180deg)";
    document.getElementById("event-menu-btn")?.setAttribute("aria-expanded", "true");
}