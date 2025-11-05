import { Routes } from '@angular/router';
import { authRoutes } from './routes/auth.routes';

export const routes: Routes = [
    { path: 'auth', children: authRoutes },
    { path: 'app', loadComponent: () => import('./layouts/app-layout/app-layout.component').then(m => m.AppLayoutComponent) },
];
