import { Component, ChangeDetectionStrategy, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NotificationService } from '../../../services/notification.service';
import { SnackbarComponent } from './snackbar.component';

@Component({
  selector: 'app-snackbar-container',
  standalone: true,
  imports: [CommonModule, SnackbarComponent],
  template: `
    <div class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] flex flex-col gap-3 pointer-events-none">
      @for (notification of notificationService.notifications$ | async; track notification.id) {
        <app-snackbar 
          class="pointer-events-auto"
          [notification]="notification"
          (dismissed)="onDismiss($event)">
        </app-snackbar>
      }
    </div>
  `,
  styles: [],
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class SnackbarContainerComponent {
  notificationService = inject(NotificationService);

  onDismiss(id: string): void {
    this.notificationService.dismiss(id);
  }
}
