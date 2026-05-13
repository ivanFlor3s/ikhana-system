import { Injectable, Type } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface SideDetailConfig<T = any> {
    component: Type<T>;
    data?: any;
}

@Injectable({
    providedIn: 'root'
})
export class SideDetailService {
    private isOpenSubject = new BehaviorSubject<boolean>(false);
    private componentSubject = new BehaviorSubject<SideDetailConfig | null>(null);
    private widthSubject = new BehaviorSubject<number>(500); // Default width

    public isOpen$: Observable<boolean> = this.isOpenSubject.asObservable();
    public component$: Observable<SideDetailConfig | null> = this.componentSubject.asObservable();
    public width$: Observable<number> = this.widthSubject.asObservable();

    /**
     * Opens the side detail panel with the specified component and data
     * @param component The component to render
     * @param data Optional data to pass to the component as inputs
     */
    open<T>(component: Type<T>, data?: any): void {
        this.componentSubject.next({ component, data });
        this.isOpenSubject.next(true);
    }

    /**
     * Closes the side detail panel
     */
    close(): void {
        this.isOpenSubject.next(false);
        // Clear component after animation completes
        setTimeout(() => {
            if (!this.isOpenSubject.value) {
                this.componentSubject.next(null);
            }
        }, 300);
    }

    /**
     * Updates the width of the side detail panel
     * @param width The new width in pixels
     */
    setWidth(width: number): void {
        // Clamp width between min and max
        const clampedWidth = Math.max(400, Math.min(800, width));
        this.widthSubject.next(clampedWidth);
    }

    /**
     * Gets the current width
     */
    getWidth(): number {
        return this.widthSubject.value;
    }
}
