import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
@Component({
  selector: 'app-app-layout',
  imports: [RouterOutlet, MatIconModule],
  templateUrl: './app-layout.component.html',
  styleUrl: './app-layout.component.css'
})
export class AppLayoutComponent {
  collapsed = signal(false);

  toggleSidebar() {
    this.collapsed.update(v => !v);
  }
}
