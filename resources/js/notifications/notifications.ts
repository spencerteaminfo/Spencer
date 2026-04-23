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

    const escapeHtml = (value: string): string =>
        value
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');

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

        listElement.innerHTML = notifications
            .map((notification) => {
                const notificationIsRead = isRead(notification);
                const actionLabel = notificationIsRead ? 'Read' : 'Mark as read';

                return `
                    <div class="card border-0 shadow-sm rounded-pill p-2 px-3" data-id="${notification.id}">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="ratio ratio-1x1 bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <img src="${escapeHtml(bellIcon)}" alt="notif" class="h-50 w-auto opacity-75">
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="mb-0 text-dark fw-medium text-truncate">${escapeHtml(getMessage(notification))}</p>
                                <small class="text-muted opacity-75">${escapeHtml(formatTime(notification.created_at))}</small>
                            </div>
                            <div class="ms-2 d-none d-sm-block">
                                <button class="btn btn-sm btn-light rounded-pill border px-3 shadow-none notification-read-button"
                                    data-id="${notification.id}"
                                    ${notificationIsRead ? 'disabled' : ''}
                                    type="button">
                                    ${actionLabel}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            })
            .join('');
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
