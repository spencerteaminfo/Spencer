import api from './bootstrap';

addEventListener("DOMContentLoaded", () => {
    const firstName = document.getElementById("firstName") as HTMLInputElement;
    const lastName = document.getElementById("lastName") as HTMLInputElement;
    const settingInputs = document.querySelectorAll('input[name^="options["], select[name^="options["]');
    const editFirstBtn = document.getElementById("editFirstName");
    const editLastBtn = document.getElementById("editLastName");
    const saveSuccess = document.getElementById("saveSuccess");
    const profilePicContainer = document.getElementById("profilePicContainer");
    const profilePicInput = document.getElementById("profilePicInput") as HTMLInputElement;
    const avatarDisplay = document.getElementById("avatarDisplay") as HTMLImageElement;
    const deleteAccountForm = document.getElementById("deleteAccountForm") as HTMLInputElement;

    if (deleteAccountForm) {
        deleteAccountForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            try {
                await api.get('/sanctum/csrf-cookie');
                await api.delete('/api/user');
                window.location.href = "/login";
            } catch (e) {
                console.error(e);
                alert("Nepodařilo se smazat účet.");
            }
        });
    }

    const saveProfile = async () => {
        const formData = new FormData();
        formData.append('first_name', firstName.value);
        formData.append('last_name', lastName.value);

        formData.append('_method', 'PATCH'); // spoofs post into patch

        if (profilePicInput.files?.[0]) {
            formData.append('profile_picture', profilePicInput.files[0]);
        }

        try {
            const response = await api.post('/api/user/profile', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            if (response.data.path && avatarDisplay) {
                avatarDisplay.src = `/storage/${response.data.path}?t=${new Date().getTime()}`;
            }

            saveSuccess?.classList.remove("d-none");
            setTimeout(() => saveSuccess?.classList.add("d-none"), 2000);
        } catch (e) {
            console.error(e);
        }
    };

    editFirstBtn?.addEventListener("click", () => firstName.focus());
    editLastBtn?.addEventListener("click", () => lastName.focus());
    firstName?.addEventListener("change", saveProfile);
    lastName?.addEventListener("change", saveProfile);
    profilePicContainer?.addEventListener("click", () => profilePicInput.click());
    profilePicInput?.addEventListener("change", () => {
        if (profilePicInput.files && profilePicInput.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                if (avatarDisplay) avatarDisplay.src = e.target?.result as string;
            };
            reader.readAsDataURL(profilePicInput.files[0]);
            saveProfile();
        }
    });

    settingInputs.forEach(input => {
        input.addEventListener("change", async () => {
            let selectedIds: string[] = [];

            // checkbox
            const checkedBoxes = document.querySelectorAll('input[name^="options["]:checked');
            checkedBoxes.forEach(checkedBox => {
                selectedIds.push((checkedBox as HTMLInputElement).value);
            });

            // dropdown
            const selects = document.querySelectorAll('select[name^="options["]');
            selects.forEach(sel => {
                const val = (sel as HTMLSelectElement).value;
                if (val) selectedIds.push(val);
            });

            try {
                const response = await api.patch('/api/user/settings', {
                    options: selectedIds
                });
                window.location.reload();
            } catch (e) {
                console.error("Chyba při ukládání nastavení:", e);
            }
        });
    });

    const openDeleteDialog = document.getElementById("openDeleteDialog");
    const deleteMenu = document.getElementById("deleteMenu") as HTMLElement;
    const cancelDelete = document.getElementById("cancelDelete");
    const submitDelete = deleteMenu?.querySelector('button[type="submit"]') as HTMLButtonElement;
    let interval: number;

    openDeleteDialog?.addEventListener("click", (e) => {
        e.preventDefault();
        deleteMenu.classList.remove("d-none");

        let count = 10;
        submitDelete.disabled = true;
        submitDelete.innerText = `Wait ${count}s`;

        interval = window.setInterval(() => {
            count--;
            submitDelete.innerText = `Wait ${count}s`;
            if (count <= 0) {
                clearInterval(interval);
                submitDelete.disabled = false;
                submitDelete.innerText = "Delete account";
            }
        }, 1000);
    });

    const closeDelete = () => {
        deleteMenu.classList.add("d-none");
        clearInterval(interval);
    };

    cancelDelete?.addEventListener("click", closeDelete);

    deleteMenu?.addEventListener("click", (e) => {
        if (e.target === deleteMenu) closeDelete();
    });

    const deletePfpBtn = document.getElementById("DeletePFP");
    deletePfpBtn?.addEventListener("click", async (e) => {
        e.preventDefault();
        if (!confirm("Smazat profilovku?")) return;
        try {
            const response = await api.patch('/api/user/profile', { delete_avatar: true });
            if (response.data.status === 'success') {
                window.location.reload();
            }
        } catch (e) {
            console.error(e);
        }
    });
});
