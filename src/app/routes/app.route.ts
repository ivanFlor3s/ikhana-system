import { Routes } from '@angular/router';
import { ProveedoresPageComponent } from '../pages/proveedores-page/proveedores-page.component';
import { RubrosPageComponent } from 'app/pages/rubros-page/rubros-page.component';
import { authGuard } from '@guards/auth.guard';

export const appRoutes: Routes = [
    {
        path: '',
        canActivate: [authGuard],
        canActivateChild: [authGuard],
        loadComponent: () =>
            import('../layouts/app-layout/app-layout.component')
                .then(m => m.AppLayoutComponent),
        children: [
            {
                path: 'proveedores',
                component: ProveedoresPageComponent,


            },
            {
                path: 'rubros',
                component: RubrosPageComponent,
            }
        ]
    }
];

