import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, Component, Input, forwardRef } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';
import { NameValue } from '@models/name-value.model';
import { ChipComponent } from '../chip/chip';

@Component({
    selector: 'app-chips-selector',
    imports: [CommonModule, ChipComponent],
    template: `
    <div class="flex flex-wrap gap-2">
      @for (chip of chips; track chip.value) {
        <app-chip
          [chip]="chip"
          [selected]="isSelected(chip.value)"
          (click)="toggleChip(chip.value)"
        />
      }
    </div>
  `,
    changeDetection: ChangeDetectionStrategy.OnPush,
    host: {
        class: 'block'
    },
    providers: [
        {
            provide: NG_VALUE_ACCESSOR,
            useExisting: forwardRef(() => ChipsSelectorComponent),
            multi: true,
        },
    ],
})
export class ChipsSelectorComponent implements ControlValueAccessor {
    @Input() chips: NameValue[] = [];
    @Input() multiple = false;

    private _value: number[] = [];
    private _onChange: (value: number[]) => void = () => { };
    private _onTouched: () => void = () => { };

    get value(): number[] {
        return this._value;
    }

    set value(val: number[]) {
        this._value = val || [];
        this._onChange(this._value);
    }

    isSelected(chipValue: number): boolean {
        return this._value.includes(chipValue);
    }

    toggleChip(chipValue: number): void {
        this._onTouched();

        if (this.multiple) {
            // Multiple selection mode
            const index = this._value.indexOf(chipValue);
            if (index > -1) {
                // Remove if already selected
                this.value = this._value.filter(v => v !== chipValue);
            } else {
                // Add if not selected
                this.value = [...this._value, chipValue];
            }
        } else {
            // Single selection mode
            if (this._value.includes(chipValue)) {
                // Deselect if already selected
                this.value = [];
            } else {
                // Select only this chip
                this.value = [chipValue];
            }
        }
    }

    // ControlValueAccessor implementation
    writeValue(value: number[]): void {
        this._value = value || [];
        // Don't call _onChange here - writeValue is called by the form control
        // and should not trigger a change event back to the form
    }

    registerOnChange(fn: (value: number[]) => void): void {
        this._onChange = (value: number[]) => {
            fn(value);
        };
    }

    registerOnTouched(fn: () => void): void {
        this._onTouched = fn;
    }

    setDisabledState?(isDisabled: boolean): void {
        // TODO: Implement disabled state if needed
    }
}
