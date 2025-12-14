export type NotificationType = 'success' | 'warning' | 'info' | 'error';

export interface NotificationConfig {
    id?: string;
    type: NotificationType;
    title: string;
    description?: string;
    icon?: string;
    duration?: number; // milliseconds, 0 for no auto-dismiss
    dismissible?: boolean;
    loading?: boolean; // Show spinner instead of icon
}

export interface Notification extends Required<NotificationConfig> {
    id: string;
}
