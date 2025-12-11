import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { SideDetailComponent } from '../../components/side-detail/side-detail.component';
import { SideMenuComponent } from '../../core/components/side-menu/side-menu.component';
import { MenuItem } from '../../core/models/menu-item.interface';

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
      icon: 'dashboard',
      route: '/app/dashboard'
    },
    {
      label: 'Product',
      icon: 'inventory_2',
      children: [
        { label: 'Overview', icon: '', route: '/app/product/overview' },
        { label: 'Drafts', icon: '', route: '/app/product/drafts', badge: { value: 3, color: 'accent' } },
        { label: 'Released', icon: '', route: '/app/product/released' },
        { label: 'Comments', icon: '', route: '/app/product/comments' },
        { label: 'Scheduled', icon: '', route: '/app/product/scheduled', badge: { value: 8, color: 'success' } }
      ]
    },
    {
      label: 'Customers',
      icon: 'people',
      route: '/app/customers'
    },
    {
      label: 'Shop',
      icon: 'store',
      route: '/app/shop'
    },
    {
      label: 'Income',
      icon: 'trending_up',
      route: '/app/income'
    },
    {
      label: 'Promote',
      icon: 'campaign',
      route: '/app/promote'
    },
    {
      label: 'Proveedores',
      icon: 'local_shipping',
      route: '/app/proveedores'
    }
  ];
}
