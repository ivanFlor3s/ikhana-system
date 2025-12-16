import { LucideIconData } from 'lucide-angular';

export interface MenuItem {
    label: string;
    icon: LucideIconData; // Lucide icon component (e.g., Package, Home, Users)
    route?: string;
    badge?: MenuBadge;
    children?: MenuItem[];
    expanded?: boolean;
}

export interface MenuBadge {
    value: number | string;
    color: 'primary' | 'accent' | 'warn' | 'success';
}
