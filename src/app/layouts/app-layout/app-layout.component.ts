import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { SideDetailComponent } from '../../components/side-detail/side-detail.component';
@Component({
  selector: 'app-app-layout',
  imports: [RouterOutlet, MatIconModule, SideDetailComponent],
  templateUrl: './app-layout.component.html',
  styleUrl: './app-layout.component.css'
})
export class AppLayoutComponent {
  collapsed = signal(false);

  toggleSidebar() {
    this.collapsed.update(v => !v);
  }
}
