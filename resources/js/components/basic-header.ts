export const initLanguageSwitcher = () => {
    const langSelect = document.querySelector('select[name="language"]') as HTMLSelectElement;

    if (langSelect) {
        langSelect.addEventListener('change', () => {
            const selectedLang = langSelect.value;

            const url = new URL(window.location.href);
            url.searchParams.set('lang', selectedLang);

            window.location.href = url.toString();
        });
    }
};

document.addEventListener('DOMContentLoaded', initLanguageSwitcher);
