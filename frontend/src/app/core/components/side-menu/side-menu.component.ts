import { Component, computed, inject, input, model, output, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { LucideAngularModule, ChevronLeft, ChevronRight, ChevronDown, ChevronUp, Menu } from 'lucide-angular';
import { MenuItem } from '../../models/menu-item.interface';
import { UserDetailComponent } from "../user-detail/user-detail.component";
import { AuthService } from '@services/auth.service';

@Component({
    selector: 'app-side-menu',
    standalone: true,
    imports: [CommonModule, RouterModule, LucideAngularModule, UserDetailComponent],
    templateUrl: './side-menu.component.html',
    styleUrl: './side-menu.component.css'
})
export class SideMenuComponent {
    // Lucide icons
    readonly ChevronLeftIcon = ChevronLeft;
    readonly ChevronRightIcon = ChevronRight;
    readonly ChevronDownIcon = ChevronDown;
    readonly ChevronUpIcon = ChevronUp;
    readonly MenuIcon = Menu;

    // Signal inputs
    menuItems = input.required<MenuItem[]>();
    appTitle = input<string>('Kikhana App');
    appLogo = input<string | null>(null);

    // Two-way binding for collapsed state
    collapsed = model<boolean>(false);

    // Track expanded state for each menu item
    expandedItems = signal<Set<string>>(new Set());

    private authService = inject(AuthService);
    name = computed(() => this.authService.name());
    role = computed(() => this.authService.role());

    toggleSidebar() {
        this.collapsed.update(v => !v);
    }

    toggleMenuItem(item: MenuItem) {
        if (!item.children || item.children.length === 0) {
            return;
        }

        this.expandedItems.update(expanded => {
            const newSet = new Set(expanded);
            if (newSet.has(item.label)) {
                newSet.delete(item.label);
            } else {
                newSet.add(item.label);
            }
            return newSet;
        });
    }

    isExpanded(item: MenuItem): boolean {
        return this.expandedItems().has(item.label);
    }

    hasChildren(item: MenuItem): boolean {
        return !!item.children && item.children.length > 0;
    }
}
