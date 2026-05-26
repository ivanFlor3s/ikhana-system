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
      label: 'Promote',
      icon: Megaphone,
      route: '/app/promote'
    },
    {
      label: 'Proveedores',
      icon: Truck,
      //TODO: Fix allow click on parent if have route
      route: '/app/proveedores',
      children: [
        { label: 'Bobinas', icon: Spool, route: '/app/proveedores/bobinas' },
      ]
    },
    {
      label: 'Rubros',
      icon: Tags,
      route: '/app/rubros'
    },
    {
      label: 'Materias Primas',
      icon: Sprout,
      children: [
        { label: 'Cobre', icon: Spool, route: '/app/materias-primas/cobre' },
        { label: 'Cuerda', icon: Spool, route: '/app/materias-primas/cuerda' },
      ]
    }
  ];
}
