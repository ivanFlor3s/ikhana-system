import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

export type BadgeVariant = 'success' | 'error' | 'warning' | 'info' | 'neutral';
export type BadgeSize = 'sm' | 'md' | 'lg';

@Component({
  selector: 'app-badge',
  imports: [CommonModule],
  templateUrl: './badge.component.html',
  styleUrl: './badge.component.css'
})
export class BadgeComponent {
  /**
   * The text to display in the badge
   */
  @Input() label: string = '';

  /**
   * The visual variant of the badge
   * - success: Green (for positive states, S, CUMPLE, etc.)
   * - error: Red (for negative states, N, NO CUMPLE, etc.)
   * - warning: Yellow/Orange (for warnings)
   * - info: Blue (for informational states)
   * - neutral: Gray (for neutral states)
   */
  @Input() variant: BadgeVariant = 'neutral';

  /**
   * The size of the badge
   */
  @Input() size: BadgeSize = 'md';

  /**
   * Optional icon to display before the label
   */
  @Input() icon?: string;

  /**
   * Whether to make the badge rounded (pill shape)
   */
  @Input() rounded: boolean = true;
}
