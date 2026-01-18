import { Routes } from '@angular/router';
import { ProveedoresPageComponent } from '../pages/proveedores-page/proveedores-page.component';
import { RubrosPageComponent } from 'app/pages/rubros-page/rubros-page.component';
import { MateriasPrimasPageComponent } from 'app/pages/materias-primas-page/materias-primas-page.component';

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


            },
            {
                path: 'rubros',
                component: RubrosPageComponent,
            },
            {
                path: 'materias-primas',
                component: MateriasPrimasPageComponent,
            }
        ]
    }
];

