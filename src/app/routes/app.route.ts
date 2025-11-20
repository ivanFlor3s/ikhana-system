import { Routes } from '@angular/router';
import { CategoryPageComponent } from '../pages/category-page/category-page.component';

export const appRoutes: Routes = [
    {
        path: '',
        loadComponent: () =>
            import('../layouts/app-layout/app-layout.component')
                .then(m => m.AppLayoutComponent),
        children: [
            {
                path: 'categories',
                component: CategoryPageComponent,

            }
        ]
    }
];
