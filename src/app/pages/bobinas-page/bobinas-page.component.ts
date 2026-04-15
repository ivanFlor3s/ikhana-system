import { Component } from '@angular/core';
import { PageHeaderComponent } from "@shared/components/page-header/page-header.component";
import { Spool, LucideAngularModule } from 'lucide-angular';

@Component({
  selector: 'app-bobinas-page',
  imports: [PageHeaderComponent, LucideAngularModule],
  templateUrl: './bobinas-page.component.html',
  styleUrl: './bobinas-page.component.css'
})
export class BobinasPageComponent {

  readonly SpoonIcon = Spool

}
