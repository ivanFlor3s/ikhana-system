import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { SideDetailComponent } from '../../components/side-detail/side-detail.component';
import { SideMenuComponent } from '../../core/components/side-menu/side-menu.component';
import { MenuItem } from '../../core/models/menu-item.interface';
import {
  LayoutDashboard,
  Package,
  Users,
  Store,
  TrendingUp,
  Megaphone,
  Truck,
  Tags,
  Sprout,
  Spool
} from 'lucide-angular';

@Component({
  selector: 'app-app-layout',
  imports: [RouterOutlet, SideDetailComponent, SideMenuComponent],
  templateUrl: './app-layout.component.html',
  styleUrl: './app-layout.component.css'
})
export class AppLayoutComponent {
  menuItems: MenuItem[] = [
    {
      label: 'Dashboard',
      icon: LayoutDashboard,
      route: '/app/dashboard'
    },
    {
      label: 'Product',
      icon: Package,
      children: [
        { label: 'Overview', icon: Package, route: '/app/product/overview' },
        { label: 'Drafts', icon: Package, route: '/app/product/drafts', badge: { value: 3, color: 'accent' } },
        { label: 'Released', icon: Package, route: '/app/product/released' },
        { label: 'Comments', icon: Package, route: '/app/product/comments' },
        { label: 'Scheduled', icon: Package, route: '/app/product/scheduled', badge: { value: 8, color: 'success' } }
      ]
    },
    {
      label: 'Customers',
      icon: Users,
      route: '/app/customers'
    },
    {
      label: 'Shop',
      icon: Store,
      route: '/app/shop'
    },
    {
      label: 'Income',
      icon: TrendingUp,
      route: '/app/income'
    },
    {
      label: 'Promote',
      icon: Megaphone,
      route: '/app/promote'
    },
    {
      label: 'Proveedores',
      icon: Truck,
      route: '/app/proveedores'
    },
    {
      label: 'Rubros',
      icon: Tags,
      route: '/app/rubros'
    },
    {
      label: 'Materias Primas',
      icon: Sprout,
      route: '/app/materias-primas',
      children: [
        { label: 'Cobre', icon: Spool, route: '/app/materias-primas/cobre' },
      ]
    }
  ];
}
