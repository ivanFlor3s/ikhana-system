import { Component, inject, Output, EventEmitter, OnInit, OnDestroy } from '@angular/core';
import { FormBuilder, ReactiveFormsModule } from '@angular/forms';
import { InputComponent } from '@shared/components/input/input';
import { MatIcon } from '@angular/material/icon';
import { Subject, debounceTime, takeUntil } from 'rxjs';

@Component({
  selector: 'app-bobinas-filter',
  imports: [ReactiveFormsModule, InputComponent, MatIcon],
  templateUrl: './bobinas-filter.component.html',
})
export class BobinasFilterComponent implements OnInit, OnDestroy {
  private _fb = inject(FormBuilder);
  private destroy$ = new Subject<void>();

  @Output() filterChange = new EventEmitter<{ search?: string }>();

  forma = this._fb.group({
    search: [''],
  });

  ngOnInit(): void {
    this.forma.valueChanges
      .pipe(debounceTime(400), takeUntil(this.destroy$))
      .subscribe((v) => {
        this.filterChange.emit({
          search: v.search || undefined,
        });
      });
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }
}
