export interface MenuItem {
    label: string;
    icon: string; // Material icon name (e.g., 'dashboard', 'local_shipping')
    route?: string;
    badge?: MenuBadge;
    children?: MenuItem[];
    expanded?: boolean;
}

export interface MenuBadge {
    value: number | string;
    color: 'primary' | 'accent' | 'warn' | 'success';
}
