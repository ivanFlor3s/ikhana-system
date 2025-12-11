import { Routes } from '@angular/router';
import { ProveedoresPageComponent } from '../pages/proveedores-page/proveedores-page.component';

export const appRoutes: Routes = [
    {
        path: '',
        loadComponent: () =>
            import('../layouts/app-layout/app-layout.component')
                .then(m => m.AppLayoutComponent),
        children: [
            {
                path: 'proveedores',
                component: ProveedoresPageComponent,

            }
        ]
    }
];

