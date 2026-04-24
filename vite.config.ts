import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/custom.css',
                'resources/js/components/basic-header.ts',
                'resources/js/components/sidebar/sidebar.ts',
                'resources/js/components/sidebar/sidebarToggleProvider.ts',
                'resources/js/event/createEvent.ts',
                'resources/js/event/detailEvent.ts',
                'resources/js/event/loadEvents.ts',
                'resources/js/group/createGroup.ts',
                'resources/js/group/loadGroup.ts',
                'resources/js/group/showGroup.ts',
                'resources/js/main/loadMainPage.ts',
                'resources/js/notifications/notifications.ts',
                'resources/js/settings/settingsUser.ts',
                'resources/js/utils/auth.ts',
                'resources/js/utils/searchMenu.ts',
                'resources/js/utils/showImg.ts',
                'resources/svg/arrow-in-square.svg',
                'resources/svg/arrow-left.svg',
                'resources/svg/bell.svg',
                'resources/svg/check.svg',
                'resources/svg/chevron-down.svg',
                'resources/svg/clock.svg',
                'resources/svg/edit-2.svg',
                'resources/svg/edit.svg',
                'resources/svg/file.svg',
                'resources/svg/home.svg',
                'resources/svg/inbox.svg',
                'resources/svg/list.svg',
                'resources/svg/log-out.svg',
                'resources/svg/plus-circle.svg',
                'resources/svg/plus.svg',
                'resources/svg/search-input.svg',
                'resources/svg/search.svg',
                'resources/svg/settings.svg',
                'resources/svg/trash.svg',
                'resources/svg/user.svg',
                'resources/svg/users.svg',
                'resources/svg/x.svg',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': 'resources/js',
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
