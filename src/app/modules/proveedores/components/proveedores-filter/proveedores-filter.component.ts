import { Component, inject, Input } from '@angular/core';
import { FormBuilder, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { InputComponent } from '@shared/components/input/input';
import { MatIcon } from "@angular/material/icon";
import { ChipsSelectorComponent } from '@shared/components/chips-selector/chips-selector';
import { NameValue } from '@models/name-value.model';

@Component({
  selector: 'app-proveedores-filter',
  imports: [ReactiveFormsModule, FormsModule, InputComponent, MatIcon, ChipsSelectorComponent],
  templateUrl: './proveedores-filter.component.html',

})
export class ProveedoresFilterComponent {
  private _fb = inject(FormBuilder);

  @Input() rubros: NameValue[] = [];

  forma = this._fb.group({
    name: [""],
    rubros: [[]]
  })


  onSubmit() {
    console.log(this.forma.value);
  }
}
