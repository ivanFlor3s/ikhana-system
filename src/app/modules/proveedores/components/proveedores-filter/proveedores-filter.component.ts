import { Component, inject } from '@angular/core';
import { MatLabel, MatFormField, MatInput } from "@angular/material/input";
import { FormBuilder, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Button } from '@shared/components/button/button';

@Component({
  selector: 'app-proveedores-filter',
  imports: [MatLabel, MatFormField, MatInput, ReactiveFormsModule, FormsModule, Button],
  templateUrl: './proveedores-filter.component.html',

})
export class ProveedoresFilterComponent {
  private _fb = inject(FormBuilder);

  forma = this._fb.group({
    name: [""]
  })


  onSubmit() {
    console.log(this.forma.value);
  }
}
