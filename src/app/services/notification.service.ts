import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';
import { Notification, NotificationConfig, NotificationType } from '../core/models/notification.model';

@Injectable({
    providedIn: 'root'
})
export class NotificationService {
    private notificationsSubject = new BehaviorSubject<Notification[]>([]);
    public notifications$: Observable<Notification[]> = this.notificationsSubject.asObservable();

    private defaultDuration = 5000; // 5 seconds
    private notificationCounter = 0;

    /**
     * Show a notification with custom configuration
     */
    show(config: NotificationConfig): string {
        const notification: Notification = {
            id: config.id || `notification-${++this.notificationCounter}-${Date.now()}`,
            type: config.type,
            title: config.title,
            description: config.description || '',
            icon: config.icon || this.getDefaultIcon(config.type),
            duration: config.duration !== undefined ? config.duration : this.defaultDuration,
            dismissible: config.dismissible !== undefined ? config.dismissible : true,
            loading: config.loading || false,
        };

        const currentNotifications = this.notificationsSubject.value;
        this.notificationsSubject.next([...currentNotifications, notification]);

        // Auto-dismiss if duration > 0
        if (notification.duration > 0) {
            setTimeout(() => {
                this.dismiss(notification.id);
            }, notification.duration);
        }

        return notification.id;
    }

    /**
     * Update an existing notification
     */
    update(id: string, updates: Partial<NotificationConfig>): void {
        const currentNotifications = this.notificationsSubject.value;
        const updatedNotifications = currentNotifications.map(n => {
            if (n.id === id) {
                return {
                    ...n,
                    ...updates,
                    icon: updates.icon || n.icon,
                };
            }
            return n;
        });
        this.notificationsSubject.next(updatedNotifications);
    }

    /**
     * Dismiss a notification by ID
     */
    dismiss(id: string): void {
        const currentNotifications = this.notificationsSubject.value;
        const filteredNotifications = currentNotifications.filter(n => n.id !== id);
        this.notificationsSubject.next(filteredNotifications);
    }

    /**
     * Show a success notification
     */
    success(title: string, description?: string, duration?: number): string {
        return this.show({
            type: 'success',
            title,
            description,
            duration
        });
    }

    /**
     * Show a warning notification
     */
    warning(title: string, description?: string, duration?: number): string {
        return this.show({
            type: 'warning',
            title,
            description,
            duration
        });
    }

    /**
     * Show an info notification
     */
    info(title: string, description?: string, duration?: number): string {
        return this.show({
            type: 'info',
            title,
            description,
            duration
        });
    }

    /**
     * Show an error notification
     */
    error(title: string, description?: string, duration?: number): string {
        return this.show({
            type: 'error',
            title,
            description,
            duration
        });
    }

    /**
     * Clear all notifications
     */
    clearAll(): void {
        this.notificationsSubject.next([]);
    }

    /**
     * Get default icon for notification type
     */
    private getDefaultIcon(type: NotificationType): string {
        const icons = {
            success: 'check_circle',
            warning: 'warning',
            info: 'info',
            error: 'cancel'
        };
        return icons[type];
    }
}
