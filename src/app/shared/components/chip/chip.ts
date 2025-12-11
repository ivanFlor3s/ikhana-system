import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, Component, Input } from '@angular/core';
import { NameValue } from '@models/name-value.model';

@Component({
    selector: 'app-chip',
    imports: [CommonModule],
    template: `
    <div 
      [ngClass]="chipClasses()"
      class="inline-flex items-center gap-1 transition-all duration-200 cursor-pointer select-none"
    >
      <span class="text-xs font-medium">{{ chip.name }}</span>
    </div>
  `,
    changeDetection: ChangeDetectionStrategy.OnPush,
    host: {
        class: 'inline-block'
    }
})
export class ChipComponent {
    @Input() chip!: NameValue;
    @Input() selected = false;

    chipClasses(): string {
        const parts: string[] = [
            'px-2',
            'py-1',
            'rounded-full',
            'border',
            'transition-all',
            'duration-200'
        ];

        if (this.selected) {
            parts.push(
                'bg-neutral-800',
                'text-white',
                'border-neutral-800',
                'shadow-sm'
            );
        } else {
            parts.push(
                'bg-white',
                'text-neutral-700',
                'border-neutral-300',
                'hover:border-neutral-400',
                'hover:bg-neutral-50'
            );
        }

        return parts.join(' ');
    }
}
