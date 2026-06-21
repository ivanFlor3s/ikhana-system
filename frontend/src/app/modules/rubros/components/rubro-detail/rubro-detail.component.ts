import { Component, Input, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatProgressSpinner } from "@angular/material/progress-spinner";
import { CategoryApiService, CategoryDetailResponse } from '@generated/category-api.service';

@Component({
  selector: 'app-rubro-detail',
  imports: [CommonModule, MatProgressSpinner],
  templateUrl: './rubro-detail.component.html',
  styleUrl: './rubro-detail.component.css'
})
export class RubroDetailComponent implements OnInit {
  @Input() rubroId!: number;
  private categoryApi = inject(CategoryApiService);
  rubro: CategoryDetailResponse | null = null;
  isLoading = true;
  error: string | null = null;

  ngOnInit(): void { if (this.rubroId) { this.loadRubroDetails(); } }

  private loadRubroDetails(): void {
    this.isLoading = true;
    this.error = null;
    this.categoryApi.get(this.rubroId).subscribe({
      next: (rubro) => {
        this.rubro = rubro;
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Error loading rubro:', err);
        this.error = 'Error al cargar los detalles del rubro';
        this.isLoading = false;
      }
    });
  }

  copyToClipboard(text: unknown): void {
    const value = String(text ?? '');
    navigator.clipboard.writeText(value).then(
      () => { console.log('Copied to clipboard:', value); },
      (err) => { console.error('Failed to copy to clipboard:', err); }
    );
  }
}
