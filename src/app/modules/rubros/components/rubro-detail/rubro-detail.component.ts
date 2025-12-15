import { Component, Input, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatProgressSpinner } from "@angular/material/progress-spinner";
import { RubroService } from '../../../../services/rubro.service';
import { Rubro } from '../../../../models/rubro.model';

@Component({
  selector: 'app-rubro-detail',
  imports: [CommonModule, MatProgressSpinner],
  templateUrl: './rubro-detail.component.html',
  styleUrl: './rubro-detail.component.css'
})
export class RubroDetailComponent implements OnInit {
  @Input() rubroId!: number;

  private rubroService = inject(RubroService);

  rubro: Rubro | null = null;
  isLoading = true;
  error: string | null = null;

  ngOnInit(): void {
    if (this.rubroId) {
      this.loadRubroDetails();
    }
  }

  private loadRubroDetails(): void {
    this.isLoading = true;
    this.error = null;

    this.rubroService.getRubroById(this.rubroId).subscribe({
      next: (response) => {
        if (response.success) {
          this.rubro = response.data;
        } else {
          this.error = response.message || 'Error al cargar el rubro';
        }
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Error loading rubro:', err);
        this.error = 'Error al cargar los detalles del rubro';
        this.isLoading = false;
      }
    });
  }

  /**
   * Copies text to clipboard
   */
  copyToClipboard(text: string): void {
    navigator.clipboard.writeText(text).then(
      () => {
        console.log('Copied to clipboard:', text);
      },
      (err) => {
        console.error('Failed to copy to clipboard:', err);
      }
    );
  }
}
