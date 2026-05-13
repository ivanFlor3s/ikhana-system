import { Component, inject, Output, EventEmitter, OnInit, OnDestroy } from '@angular/core';
import { FormBuilder, FormsModule, ReactiveFormsModule, FormGroup } from '@angular/forms';
import { InputComponent } from '@shared/components/input/input';
import { MatIcon } from "@angular/material/icon";
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatNativeDateModule } from '@angular/material/core';
import { CustomSelectComponent } from '@shared/components/custom-select/custom-select';
import { Subject, debounceTime } from 'rxjs';
import { CommonModule } from '@angular/common';
import { AppInitService } from '@services/app-init.service';

export interface RawMaterialFilterOptions {
  search?: string;
  raw_material_characteristic_id?: number;
  date_from?: string;
  date_to?: string;
}

@Component({
  selector: 'app-mp-cobre-filter',
  imports: [
    ReactiveFormsModule,
    FormsModule,
    InputComponent,
    MatIcon,
    CustomSelectComponent,
    CommonModule,
    MatDatepickerModule,
    MatFormFieldModule,
    MatInputModule,
    MatNativeDateModule
  ],
  templateUrl: './mp-cobre-filter.component.html',
  styleUrl: './mp-cobre-filter.component.css'
})
export class MpCobreFilterComponent implements OnInit, OnDestroy {
  private _fb = inject(FormBuilder);
  private _appInitService = inject(AppInitService);
  private destroy$ = new Subject<void>();

  @Output() filterChange = new EventEmitter<RawMaterialFilterOptions>();

  characteristics = this._appInitService.rawMaterialCharacteristics;

  forma = this._fb.group({
    search: [""],
    characteristic_id: [null as number | null],
    dateRange: this._fb.group({
      start: [null as Date | null],
      end: [null as Date | null]
    })
  });

  ngOnInit(): void {
    // Listen to form changes with debounce
    this.forma.valueChanges.pipe(debounceTime(400)).subscribe((v) => {
      this.emitFilterChange(v);
    });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  emitFilterChange(formValue: any): void {
    const dateRange = formValue.dateRange;

    this.filterChange.emit({
      search: formValue.search || undefined,
      raw_material_characteristic_id: formValue.characteristic_id || undefined,
      date_from: dateRange?.start ? this.formatDate(dateRange.start) : undefined,
      date_to: dateRange?.end ? this.formatDate(dateRange.end) : undefined
    });
  }

  formatDate(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  clearFilters(): void {
    this.forma.reset({
      search: "",
      characteristic_id: null,
      dateRange: {
        start: null,
        end: null
      }
    });
  }
}
