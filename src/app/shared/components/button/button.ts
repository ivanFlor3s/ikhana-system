import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, Component, HostListener, Input, signal } from '@angular/core';

type Appearance = 'primary' | 'secondary';
type Variant = 'solid' | 'outlined';
type Size = 'sm' | 'md' | 'lg';

@Component({
  selector: 'app-button',
  imports: [CommonModule],
  templateUrl: './button.html',
  styleUrl: './button.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class Button {

  /** primary | secondary (azure | neutral) */
  @Input() appearance: Appearance = 'primary';

  /** solid | outlined */
  @Input() variant: Variant = 'solid';

  /** sm | md | lg */
  @Input() size: Size = 'md';

  /** disabled */
  @Input() disabled = false;

  /** button type (button | submit | reset) */
  @Input() type: 'button' | 'submit' | 'reset' = 'button';


  @HostListener('click', ['$event'])
  onClick(event: Event): void {
    if (this.disabled) {
      event.preventDefault();
      event.stopImmediatePropagation();
    }
    else {
      this.isBeingClicked.set(true);
      setTimeout(() => this.isBeingClicked.set(false), 200);
    }
  }

  isBeingClicked = signal(false);

  // Computes the Tailwind classes string
  classes(): string {
    const parts: string[] = [];

    // SIZE
    if (this.size === 'sm') {
      parts.push('text-sm px-3 py-1.5 rounded-md');
    } else if (this.size === 'lg') {
      parts.push('text-base px-5 py-3 rounded-lg');
    } else {
      // md default
      parts.push('text-sm px-4 py-2 rounded-md');
    }

    // APPEARANCE + VARIANT
    if (this.appearance === 'primary') {
      // Azure / sky tones
      if (this.variant === 'solid') {
        parts.push(
          'bg-sky-600 text-white',
          'hover:bg-sky-700',
        );
      } else {
        // outlined
        parts.push(
          'bg-transparent',
          'text-sky-600',
          'border border-sky-600',
          'hover:bg-sky-300/10',
        );
      }
    } else {
      // secondary -> neutral / modern dark-ish
      if (this.variant === 'solid') {
        parts.push(
          'bg-neutral-200 text-neutral-800',
          'hover:bg-neutral-300',

        );
      } else {
        // outlined neutral
        parts.push(
          'bg-transparent',
          'text-neutral-700',
          'border border-neutral-300',
          'hover:bg-neutral-700/10',
        );
      }
    }

    // subtle elevation for solid
    if (this.variant === 'solid') {
      parts.push('shadow-md');
    } else {
      parts.push('shadow-none');
    }

    return parts.join(' ');
  }
}
