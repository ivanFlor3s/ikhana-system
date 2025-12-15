import { Component } from '@angular/core';
import { PageHeaderComponent } from "@shared/components/page-header/page-header.component";
import { Button } from "@shared/components/button/button";
import { TagsIcon, LucideAngularModule } from "lucide-angular";

@Component({
  selector: 'app-rubros-header',
  imports: [PageHeaderComponent, Button, LucideAngularModule],
  templateUrl: './rubros-header.component.html',
  styleUrl: './rubros-header.component.css'
})
export class RubrosHeaderComponent {

  readonly TagsIcon = TagsIcon;


}
