import { Component, input } from '@angular/core';
import { LucideAngularModule, User, LogOut } from 'lucide-angular';
import { Button } from "@shared/components/button/button";
import { MatTooltipModule } from '@angular/material/tooltip';
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
}
