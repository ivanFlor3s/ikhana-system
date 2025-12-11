import { Component, Input, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatProgressSpinner } from "@angular/material/progress-spinner";
import { ProviderService } from '@services/provider.service';
import { Provider } from '@models/provider.model';

@Component({
    selector: 'app-provider-detail',
    imports: [CommonModule, MatProgressSpinner],
    templateUrl: './provider-detail.component.html',
    styleUrl: './provider-detail.component.css'
})
export class ProviderDetailComponent implements OnInit {
    @Input() providerId!: number;

    private providerService = inject(ProviderService);

    provider: Provider | null = null;
    isLoading = true;
    error: string | null = null;

    ngOnInit(): void {
        if (this.providerId) {
            this.loadProviderDetails();
        }
    }

    private loadProviderDetails(): void {
        this.isLoading = true;
        this.error = null;

        this.providerService.getProviderById(this.providerId).subscribe({
            next: (response) => {
                if (response.success) {
                    this.provider = response.data;
                } else {
                    this.error = response.message || 'Error al cargar el proveedor';
                }
                this.isLoading = false;
            },
            error: (err) => {
                console.error('Error loading provider:', err);
                this.error = 'Error al cargar los detalles del proveedor';
                this.isLoading = false;
            }
        });
    }

    /**
     * Copies text to clipboard and shows visual feedback
     */
    copyToClipboard(text: string): void {
        navigator.clipboard.writeText(text).then(
            () => {
                // Success - could add a toast notification here
                console.log('Copied to clipboard:', text);
            },
            (err) => {
                console.error('Failed to copy to clipboard:', err);
            }
        );
    }

    /**
     * Formats time object to readable string
     */
    formatTime(time: { hour: number; minute: number } | undefined): string {
        if (!time) return 'N/A';
        const hour = time.hour.toString().padStart(2, '0');
        const minute = time.minute.toString().padStart(2, '0');
        return `${hour}:${minute}`;
    }
}
