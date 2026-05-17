import { Component, inject, input } from '@angular/core';
import { LucideAngularModule, User, LogOut } from 'lucide-angular';
import { Button } from "@shared/components/button/button";
import { MatTooltipModule } from '@angular/material/tooltip';
import { AuthService } from '@services/auth.service';
import { Router } from '@angular/router';
@Component({
  selector: 'app-user-detail',
  imports: [LucideAngularModule, Button, MatTooltipModule],
  templateUrl: './user-detail.component.html',
  styleUrl: './user-detail.component.css'
})
export class UserDetailComponent {
  readonly UserIcon = User;
  readonly LogOutIcon = LogOut;

  name = input.required<string>();
  role = input.required<string>();
  collapsed = input<boolean>(false);

  private authService = inject(AuthService);
  private router = inject(Router);

  logout() {
    console.log('logout clicked');
    this.authService.logout().subscribe({
      complete: () => {
        this.router.navigate(['/auth/login']);
    }});
  }
}
