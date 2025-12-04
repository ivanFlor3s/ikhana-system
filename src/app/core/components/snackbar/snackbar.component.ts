import { Component, Input, Output, EventEmitter, ChangeDetectionStrategy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { Notification } from '../../models/notification.model';

@Component({
    selector: 'app-snackbar',
    standalone: true,
    imports: [CommonModule, MatIconModule, MatButtonModule],
    templateUrl: './snackbar.component.html',
    styleUrl: './snackbar.component.css',
    changeDetection: ChangeDetectionStrategy.OnPush,
})
export class SnackbarComponent {
    @Input({ required: true }) notification!: Notification;
    @Output() dismissed = new EventEmitter<string>();

    onDismiss(): void {
        this.dismissed.emit(this.notification.id);
    }
}
