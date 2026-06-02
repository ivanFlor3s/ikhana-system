import { Component, EventEmitter, input, Output } from '@angular/core';
import { Button } from '@shared/components/button/button';
import { PageHeaderComponent } from "@shared/components/page-header/page-header.component";
import { Shell, LucideAngularModule } from "lucide-angular";

@Component({
  selector: 'app-mp-cuerda-header',
  imports: [PageHeaderComponent, LucideAngularModule, Button],
  templateUrl: './mp-cuerda-header.component.html',
  styleUrl: './mp-cuerda-header.component.css'
})
export class MpCuerdaHeaderComponent {

  readonly ShellIcon = Shell;
  showNuevoIngreso = input<boolean>(true);

  @Output() onNuevoIngreso = new EventEmitter<void>();
}
