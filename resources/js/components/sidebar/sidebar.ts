import api from "../../bootstrap";

document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById('main-sidebar');
    const collapseIcon = document.getElementById('collapse-icon');
    const eventSubmenu = document.getElementById('event-submenu');
    const eventChevron = document.getElementById('event-chevron');
    const eventMenuBtn = document.getElementById('event-menu-btn');
    const mobileDropdown = document.getElementById('mobile-event-dropdown');
    const mobileBtn = document.getElementById('mobile-event-btn');
    const toggleSidebarBtn = document.getElementById('toggle-sidebar-btn');

    if (localStorage.getItem("sidebarCollapse") === "true") {
        sidebar?.classList.add("collapsed");
        eventSubmenu?.classList.remove("ps-4");
        if (collapseIcon) collapseIcon.style.transform = 'rotate(180deg)';
    }

    if (localStorage.getItem("EventSubmenuToggle") === "true") {
        eventSubmenu?.classList.remove("d-none");
        eventMenuBtn?.setAttribute("aria-expanded", "true");
        if (eventChevron) eventChevron.style.transform = "rotate(180deg)";
    }

    eventMenuBtn?.addEventListener('click', () => {
        const isHidden = !eventSubmenu?.classList.toggle('d-none');
        localStorage.setItem("EventSubmenuToggle", String(!isHidden));
        if (eventChevron) eventChevron.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
        eventMenuBtn.setAttribute('aria-expanded', String(!isHidden));
    });

    mobileBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        mobileDropdown?.classList.toggle('d-none');
    });

    toggleSidebarBtn?.addEventListener('click', () => {
        const isCollapsed = sidebar?.classList.toggle('collapsed');
        localStorage.setItem("sidebarCollapse", String(isCollapsed));
        if (collapseIcon) collapseIcon.style.transform = isCollapsed ? 'rotate(180deg)' : 'rotate(0deg)';
        isCollapsed ? eventSubmenu?.classList.remove('ps-4') : eventSubmenu?.classList.add('ps-4');
    });

    document.addEventListener('click', (e: MouseEvent) => {
        const target = e.target as HTMLElement;
        if (mobileDropdown && !mobileDropdown.contains(target) && !mobileBtn?.contains(target)) {
            mobileDropdown.classList.add('d-none');
        }
    });

    document.getElementById('logout')?.addEventListener("submit", async (e) => {
        e.preventDefault();
        try {
            await api.post('/api/logout');
            window.location.href = "/login";
        } catch (err) {
            console.error(err);
        }
    });
});
