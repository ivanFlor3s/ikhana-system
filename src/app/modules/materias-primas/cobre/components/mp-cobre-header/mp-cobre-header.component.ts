import { Component } from '@angular/core';
import { PageHeaderComponent } from "@shared/components/page-header/page-header.component";
import { Spool, LucideAngularModule } from "lucide-angular";

@Component({
  selector: 'app-mp-cobre-header',
  imports: [PageHeaderComponent, LucideAngularModule],
  templateUrl: './mp-cobre-header.component.html',
  styleUrl: './mp-cobre-header.component.css'
})
export class MpCobreHeaderComponent {

  readonly SpoolIcon = Spool;
}
