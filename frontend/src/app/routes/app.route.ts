import { Routes } from '@angular/router';
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
                loadComponent: () => import('../pages/proveedores-root/proveedores-root.component').then(m => m.ProveedoresRootComponent),
                children: [
                    {
                        path: '',
                        loadComponent: () => import('../pages/proveedores-page/proveedores-page.component').then(m => m.ProveedoresPageComponent)
                    },
                    {
                        path: ':providerId/movimientos-bobinas',
                        loadComponent: () => import('../pages/movimientos-page/movimientos-page.component').then(m => m.MovimientosPageComponent)
                    },
                    {
                        path: 'bobinas',
                        loadComponent: () => import('../pages/bobinas-page/bobinas-page.component').then(m => m.BobinasPageComponent)
                    }

                ]
            },
            {
                path: 'rubros',
                loadComponent: () =>
                    import('app/pages/rubros-page/rubros-page.component')
                        .then(m => m.RubrosPageComponent),
            },
            {
                path: 'materias-primas',
                loadComponent: () =>
                    import('app/pages/materias-primas-page/materias-primas-page.component')
                        .then(m => m.MateriasPrimasPageComponent),
                children: [
                    {
                        path: 'cobre',
                        loadComponent: () =>
                            import('app/pages/materias-primas-cobre-page/materias-primas-cobre-page.component')
                                .then(m => m.MateriasPrimasCobrePageComponent),
                    },
                    {
                        path: 'cobre/ingreso',
                        loadComponent: () =>
                            import('app/pages/materias-primas-cobre-page/mp-cobre-ingreso-page/mp-cobre-ingreso-page.component')
                                .then(m => m.MpCobreIngresoPageComponent),
                    },
                    {
                        path: 'cobre/editar/:id',
                        loadComponent: () =>
                            import('app/pages/materias-primas-cobre-page/mp-cobre-editar-page/mp-cobre-editar-page.component')
                                .then(m => m.MpCobreEditarPageComponent),
                    },
                    {
                        path: 'cobre/detalle/:id',
                        loadComponent: () =>
                            import('app/pages/materias-primas-cobre-page/mp-cobre-detalle-page/mp-cobre-detalle-page.component')
                                .then(m => m.MpCobreDetallePageComponent),
                    }
                ]
            },

        ]
    }
];

