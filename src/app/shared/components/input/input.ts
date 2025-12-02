import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, Component, Input, forwardRef } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';

type Size = 'sm' | 'md' | 'lg';

@Component({
    selector: 'app-input',
    imports: [CommonModule],
    templateUrl: './input.html',
    changeDetection: ChangeDetectionStrategy.OnPush,
    host: {
        class: 'block'
    },
    providers: [
        {
            provide: NG_VALUE_ACCESSOR,
            useExisting: forwardRef(() => InputComponent),
            multi: true,
        },
    ],
})
export class InputComponent implements ControlValueAccessor {

    /** Placeholder text */
    @Input() placeholder = '';

    /** sm | md | lg */
    @Input() size: Size = 'md';

    /** disabled */
    @Input() disabled = false;

    /** input type (text | email | password | number | tel | url) */
    @Input() type: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' = 'text';

    /** autocomplete attribute */
    @Input() autocomplete = 'off';

    /** prefix */
    @Input() hasPrefix = false;

    // Internal value
    private _value = '';
    private _onChange: (value: string) => void = () => { };
    private _onTouched: () => void = () => { };

    get value(): string {
        return this._value;
    }

    set value(val: string) {
        if (val !== this._value) {
            this._value = val;
            this._onChange(val);
        }
    }

    // ControlValueAccessor implementation
    writeValue(value: string): void {
        this._value = value || '';
    }

    registerOnChange(fn: (value: string) => void): void {
        this._onChange = fn;
    }

    registerOnTouched(fn: () => void): void {
        this._onTouched = fn;
    }

    setDisabledState(isDisabled: boolean): void {
        this.disabled = isDisabled;
    }

    onInput(event: Event): void {
        const target = event.target as HTMLInputElement;
        this.value = target.value;
    }

    onBlur(): void {
        this._onTouched();
    }

    // Computes the Tailwind classes string for the input
    inputClasses(): string {
        const parts: string[] = [];

        // Base styles with light gray background
        parts.push(
            'w-full',
            'font-normal',
            'bg-neutral-50',
            'border border-neutral-200',
            'text-neutral-700',
            'transition-all duration-200',
            'focus:outline-none',
            'placeholder:text-neutral-400',
            'hover:bg-neutral-100',
            'hover:border-neutral-300',
            'focus:bg-white',
            'focus:border-neutral-400',
            'focus:ring-1',
            'focus:ring-neutral-300'
        );

        // SIZE
        if (this.size === 'sm') {
            parts.push('text-sm px-3 py-1.5 rounded-md');
        } else if (this.size === 'lg') {
            parts.push('text-base px-5 py-3 rounded-lg');
        } else {
            // md default
            parts.push('text-sm px-3.5 py-2 rounded-md');
        }

        // DISABLED
        if (this.disabled) {
            parts.push('opacity-50 cursor-not-allowed bg-neutral-100');
        }

        return parts.join(' ');
    }
}
