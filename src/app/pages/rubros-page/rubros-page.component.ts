import { Component } from '@angular/core';
import { RubrosHeaderComponent } from "@modules/rubros/rubros-header/rubros-header.component";
import { MatProgressSpinnerModule } from "@angular/material/progress-spinner";
import { RubrosListComponent } from "@modules/rubros/rubros-list/rubros-list.component";

@Component({
  selector: 'app-rubros-page',
  imports: [RubrosHeaderComponent, MatProgressSpinnerModule, RubrosListComponent],
  templateUrl: './rubros-page.component.html',
  styleUrl: './rubros-page.component.css'
})
export class RubrosPageComponent {

}
