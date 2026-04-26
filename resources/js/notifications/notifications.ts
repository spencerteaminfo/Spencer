import api from '../bootstrap';

type NotificationItem = {
    id: string;
    type: string;
    data: Record<string, unknown>;
    created_at: string;
    read_at: string | null;
};

type NotificationsResponse = {
    unread: NotificationItem[];
    all: NotificationItem[] | { data: NotificationItem[] };
};

document.addEventListener('DOMContentLoaded', () => {
    const listElement = document.getElementById('notificationsList');
    const emptyElement = document.getElementById('notificationsEmpty');
    const statusElement = document.getElementById('notificationsStatus');
    const readAllButton = document.getElementById('readAllButton') as HTMLButtonElement | null;

    if (!listElement || !emptyElement || !statusElement || !readAllButton) {
        return;
    }

    let notifications: NotificationItem[] = [];
    const localReadStorageKey = 'notifications_local_read_ids';
    const bellIcon = listElement.dataset.bellIcon ?? '';

    const showStatus = (message: string): void => {
        statusElement.textContent = message;
        statusElement.classList.remove('d-none');
    };

    const hideStatus = (): void => {
        statusElement.textContent = '';
        statusElement.classList.add('d-none');
    };

    const loadLocalReadIds = (): Set<string> => {
        try {
            const raw = localStorage.getItem(localReadStorageKey);
            if (!raw) {
                return new Set<string>();
            }

            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed)) {
                return new Set<string>();
            }

            return new Set<string>(parsed.filter((item): item is string => typeof item === 'string'));
        } catch {
            return new Set<string>();
        }
    };

    const saveLocalReadIds = (ids: Set<string>): void => {
        localStorage.setItem(localReadStorageKey, JSON.stringify([...ids]));
    };

    let localReadIds = loadLocalReadIds();

    const isRead = (notification: NotificationItem): boolean =>
        notification.read_at !== null || localReadIds.has(notification.id);

    const cloneTemplate = (id: string): HTMLElement | null => {
        const template = document.getElementById(id) as HTMLTemplateElement | null;
        if (!template) {
            return null;
        }

        return template.content.firstElementChild?.cloneNode(true) as HTMLElement | null;
    };

    const getMessage = (notification: NotificationItem): string => {
        const message = notification.data.message;
        const title = notification.data.title;
        const text = notification.data.text;

        if (typeof message === 'string' && message.length > 0) {
            return message;
        }

        if (typeof title === 'string' && title.length > 0) {
            return title;
        }

        if (typeof text === 'string' && text.length > 0) {
            return text;
        }

        return `Notification: ${notification.type}`;
    };

    const formatTime = (rawDate: string): string => {
        const date = new Date(rawDate);
        if (Number.isNaN(date.getTime())) {
            return 'Unknown time';
        }

        return new Intl.DateTimeFormat('cs-CZ', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(date);
    };

    const render = (): void => {
        emptyElement.classList.toggle('d-none', notifications.length > 0);

        readAllButton.disabled = notifications.length === 0 || notifications.every((notification) => isRead(notification));

        listElement.innerHTML = '';
        const fragment = document.createDocumentFragment();

        notifications.forEach((notification) => {
            const item = cloneTemplate('notification-item-template');
            if (!item) {
                return;
            }

            const notificationIsRead = isRead(notification);
            const actionLabel = notificationIsRead ? 'Read' : 'Mark as read';
            const messageEl = item.querySelector('.js-message') as HTMLElement | null;
            const timeEl = item.querySelector('.js-time') as HTMLElement | null;
            const iconEl = item.querySelector('.js-bell-icon') as HTMLImageElement | null;
            const buttonEl = item.querySelector('.notification-read-button') as HTMLButtonElement | null;

            item.dataset.id = notification.id;
            if (messageEl) messageEl.textContent = getMessage(notification);
            if (timeEl) timeEl.textContent = formatTime(notification.created_at);
            if (iconEl) iconEl.src = bellIcon;
            if (buttonEl) {
                buttonEl.dataset.id = notification.id;
                buttonEl.disabled = notificationIsRead;
                buttonEl.textContent = actionLabel;
            }

            fragment.appendChild(item);
        });

        listElement.appendChild(fragment);
    };

    const list = async (): Promise<void> => {
        try {
            const response = await api.get<NotificationsResponse>('/api/notifications');
            const all = response.data.all;
            notifications = Array.isArray(all) ? all : all.data;

            const serverReadIds = new Set(
                notifications
                    .filter((notification) => notification.read_at !== null)
                    .map((notification) => notification.id)
            );

            localReadIds = new Set([...localReadIds].filter((id) => !serverReadIds.has(id)));
            saveLocalReadIds(localReadIds);

            render();
        } catch (e) {
            console.error(e);
            showStatus('Could not load notifications. Refresh the page and try again.');
        }
    };

    const markAsRead = async (id: string): Promise<void> => {
        const now = new Date().toISOString();

        notifications = notifications.map((notification) =>
            notification.id === id ? { ...notification, read_at: notification.read_at ?? now } : notification
        );
        render();

        try {
            await api.patch(`/api/notifications/${id}/read`);
            localReadIds.delete(id);
            saveLocalReadIds(localReadIds);
            hideStatus();
        } catch (e) {
            // Fallback when backend single-read endpoint is unavailable.
            notifications = notifications.map((notification) =>
                notification.id === id ? { ...notification, read_at: null } : notification
            );
            localReadIds.add(id);
            saveLocalReadIds(localReadIds);
            render();
            showStatus('Single read is saved in this browser until API single-read is fixed.');
        }
    };

    const markAllAsRead = async (): Promise<void> => {
        try {
            await api.patch('/api/notifications/read-all');
            notifications = notifications.map((notification) => ({ ...notification, read_at: new Date().toISOString() }));
            localReadIds.clear();
            saveLocalReadIds(localReadIds);
            render();
            hideStatus();
        } catch (e) {
            console.error(e);
            showStatus('Mark all as read failed. Try again.');
        }
    };

    listElement.addEventListener('click', (event) => {
        const target = event.target as HTMLElement | null;
        const button = target?.closest('.notification-read-button') as HTMLButtonElement | null;

        if (!button || !button.dataset.id) {
            return;
        }

        markAsRead(button.dataset.id);
    });

    readAllButton.addEventListener('click', () => {
        markAllAsRead();
    });

    list();
});
