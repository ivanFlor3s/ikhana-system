import { Component, input, model, output, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { MatIconModule } from '@angular/material/icon';
import { MenuItem } from '../../models/menu-item.interface';

@Component({
    selector: 'app-side-menu',
    standalone: true,
    imports: [CommonModule, RouterModule, MatIconModule],
    templateUrl: './side-menu.component.html',
    styleUrl: './side-menu.component.css'
})
export class SideMenuComponent {
    // Signal inputs
    menuItems = input.required<MenuItem[]>();
    appTitle = input<string>('Kikhana App');
    appLogo = input<string | null>(null);

    // Two-way binding for collapsed state
    collapsed = model<boolean>(false);

    // Track expanded state for each menu item
    expandedItems = signal<Set<string>>(new Set());

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
