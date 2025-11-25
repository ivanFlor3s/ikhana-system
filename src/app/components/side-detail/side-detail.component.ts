import { Component, OnInit, OnDestroy, ViewChild, ViewContainerRef, ComponentRef, inject, Injector, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Subject, takeUntil } from 'rxjs';
import { SideDetailService, SideDetailConfig } from '../../services/side-detail.service';

@Component({
    selector: 'app-side-detail',
    imports: [CommonModule],
    templateUrl: './side-detail.component.html',
    styleUrl: './side-detail.component.css'
})
export class SideDetailComponent implements OnInit, OnDestroy {
    @ViewChild('dynamicComponentContainer', { read: ViewContainerRef })
    dynamicComponentContainer!: ViewContainerRef;

    private sideDetailService = inject(SideDetailService);
    private injector = inject(Injector);
    private destroy$ = new Subject<void>();
    private componentRef: ComponentRef<any> | null = null;

    isOpen = signal(false);
    width = 500;
    isResizing = false;
    isLoading = false;

    ngOnInit(): void {
        // Subscribe to open/close state
        this.sideDetailService.isOpen$
            .pipe(takeUntil(this.destroy$))
            .subscribe(isOpen => {
                this.isOpen.set(isOpen);
            });

        // Subscribe to width changes
        this.sideDetailService.width$
            .pipe(takeUntil(this.destroy$))
            .subscribe(width => {
                this.width = width;
            });

        // Subscribe to component changes
        this.sideDetailService.component$
            .pipe(takeUntil(this.destroy$))
            .subscribe(config => {
                if (config) {
                    this.loadComponent(config);
                } else {
                    this.clearComponent();
                }
            });
    }

    ngOnDestroy(): void {
        this.destroy$.next();
        this.destroy$.complete();
        this.clearComponent();
    }

    /**
     * Loads a component dynamically into the container
     */
    private loadComponent(config: SideDetailConfig): void {
        if (!this.dynamicComponentContainer) {
            return;
        }

        this.isLoading = true;
        this.clearComponent();

        // Create the component with proper injector
        this.componentRef = this.dynamicComponentContainer.createComponent(config.component, {
            injector: this.injector
        });

        // Set inputs if data is provided
        if (config.data) {
            Object.keys(config.data).forEach(key => {
                if (this.componentRef?.instance) {
                    this.componentRef.instance[key] = config.data[key];
                }
            });
        }

        this.isLoading = false;
    }

    /**
     * Clears the current component
     */
    private clearComponent(): void {
        if (this.componentRef) {
            this.componentRef.destroy();
            this.componentRef = null;
        }
        if (this.dynamicComponentContainer) {
            this.dynamicComponentContainer.clear();
        }
    }

    /**
     * Closes the side detail panel
     */
    close(): void {
        this.sideDetailService.close();
    }

    /**
     * Starts the resize operation
     */
    onResizeStart(event: MouseEvent): void {
        event.preventDefault();
        this.isResizing = true;

        const startX = event.clientX;
        const startWidth = this.width;

        const onMouseMove = (e: MouseEvent) => {
            if (!this.isResizing) return;

            // Calculate new width (subtract because we're dragging from the left edge)
            const deltaX = startX - e.clientX;
            const newWidth = startWidth + deltaX;

            // Update width through service (it will clamp to min/max)
            this.sideDetailService.setWidth(newWidth);
        };

        const onMouseUp = () => {
            this.isResizing = false;
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    }
}
