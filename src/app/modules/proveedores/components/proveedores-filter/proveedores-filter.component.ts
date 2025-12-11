import { Component, inject, Input, Output, EventEmitter, OnInit, OnDestroy } from '@angular/core';
import { FormBuilder, FormsModule, ReactiveFormsModule } from '@angular/forms';
import { InputComponent } from '@shared/components/input/input';
import { MatIcon } from "@angular/material/icon";
import { ChipsSelectorComponent } from '@shared/components/chips-selector/chips-selector';
import { CustomSelectComponent } from '@shared/components/custom-select/custom-select';
import { NameValue } from '@models/name-value.model';
import { Subject, debounceTime, distinctUntilChanged, takeUntil } from 'rxjs';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-proveedores-filter',
  imports: [ReactiveFormsModule, FormsModule, InputComponent, MatIcon, CustomSelectComponent, CommonModule],
  templateUrl: './proveedores-filter.component.html',
})
export class ProveedoresFilterComponent implements OnInit, OnDestroy {
  private _fb = inject(FormBuilder);
  private destroy$ = new Subject<void>();

  @Input() rubros: NameValue[] = [];
  @Output() filterChange = new EventEmitter<{ search?: string; categoryIds?: number[] }>();

  forma = this._fb.group({
    name: [""],
    rubros: [[] as number[]]
  });

  ngOnInit(): void {

    this.forma.valueChanges.pipe(debounceTime(400)).subscribe((v) => {
      this.emitFilterChange(v);
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  emitFilterChange(formValue: Partial<{ name: string | null; rubros: number[] | null }>): void {
    this.filterChange.emit({
      search: formValue.name || undefined,
      categoryIds: formValue.rubros || []
    });
  }
}
