import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, ChangeDetectorRef, Component, ElementRef, HostListener, Input, forwardRef } from '@angular/core';
import { ControlValueAccessor, FormsModule, NG_VALUE_ACCESSOR } from '@angular/forms';
import { MatIcon } from '@angular/material/icon';
import { NameValue } from '@models/name-value.model';
import { Size } from '../input/input';

@Component({
    selector: 'app-custom-select',
    imports: [CommonModule, MatIcon, FormsModule],
    templateUrl: './custom-select.html',
    styleUrls: ['./custom-select.css'],
    changeDetection: ChangeDetectionStrategy.OnPush,
    host: {
        class: 'block'
    },
    providers: [
        {
            provide: NG_VALUE_ACCESSOR,
            useExisting: forwardRef(() => CustomSelectComponent),
            multi: true,
        },
    ],
})
export class CustomSelectComponent implements ControlValueAccessor {
    @Input() options: NameValue[] = [];
    @Input() multiple = false;
    @Input() placeholder = 'Seleccionar...';
    @Input() size: Size = 'md';
    @Input() searchEnabled = false;

    isOpen = false;
    searchTerm = '';
    private _value: number[] = [];
    private _onChange: (value: number[]) => void = () => { };
    private _onTouched: () => void = () => { };

    constructor(
        private elementRef: ElementRef,
        private cdr: ChangeDetectorRef
    ) { }

    get value(): number[] {
        return this._value;
    }

    set value(val: number[]) {
        this._value = val || [];
        this._onChange(this._value);
        this.cdr.markForCheck();
    }

    get filteredOptions(): NameValue[] {
        if (!this.searchTerm) {
            return this.options;
        }
        const term = this.searchTerm.toLowerCase();
        return this.options.filter(option =>
            option.name.toLowerCase().includes(term)
        );
    }

    get selectedLabels(): string {
        if (!this._value || this._value.length === 0) {
            return this.placeholder;
        }
        const selectedOptions = this.options.filter(opt => this._value.includes(opt.value));
        return selectedOptions.map(opt => opt.name).join(', ');
    }

    toggleDropdown(): void {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.searchTerm = '';
        }
        this._onTouched();
        this.cdr.markForCheck();
    }

    isSelected(optionValue: number): boolean {
        return this._value.includes(optionValue);
    }

    toggleOption(optionValue: number, event?: Event): void {
        if (event) {
            event.stopPropagation();
        }

        if (this.multiple) {
            // Multiple selection mode
            const index = this._value.indexOf(optionValue);
            if (index > -1) {
                // Remove if already selected
                this.value = this._value.filter(v => v !== optionValue);
            } else {
                // Add if not selected
                this.value = [...this._value, optionValue];
            }
        } else {
            // Single selection mode
            if (this._value.includes(optionValue)) {
                // Deselect if already selected
                this.value = [];
            } else {
                // Select only this option
                this.value = [optionValue];
            }
            // Close dropdown in single-select mode
            this.isOpen = false;
        }
        this.cdr.markForCheck();
    }

    clearSelection(event: Event): void {
        event.stopPropagation();
        this.value = [];
        this._onTouched();
        this.cdr.markForCheck();
    }

    triggerClasses(): string {
        const parts: string[] = [];

        // SIZE
        if (this.size === 'sm') {
            parts.push('text-sm px-3 py-1.5 rounded-md');
        } else if (this.size === 'lg') {
            parts.push('text-base px-5 py-3 rounded-lg');
        } else {
            // md default
            parts.push('text-sm px-4 py-2.5 rounded-lg');
        }

        return parts.join(' ');
    }

    @HostListener('document:click', ['$event'])
    onClickOutside(event: Event): void {
        if (!this.elementRef.nativeElement.contains(event.target)) {
            this.isOpen = false;
            this.cdr.markForCheck();
        }
    }

    // ControlValueAccessor implementation
    writeValue(value: number[]): void {
        this._value = value || [];
        this.cdr.markForCheck();
    }

    registerOnChange(fn: (value: number[]) => void): void {
        this._onChange = fn;
    }

    registerOnTouched(fn: () => void): void {
        this._onTouched = fn;
    }

    setDisabledState?(isDisabled: boolean): void {
        // TODO: Implement disabled state if needed
    }
}
