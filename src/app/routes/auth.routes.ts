import { Routes } from '@angular/router';
import { guestGuard } from '@guards/guest.guard';

export const authRoutes: Routes = [
    {
        path: '',
        canActivate: [guestGuard],
        canActivateChild: [guestGuard],
        loadComponent: () =>
            import('../layouts/auth-layout/auth-layout.component')
                .then(m => m.AuthLayoutComponent),
        children: [
            { path: 'login', loadComponent: () => import('../pages/login/login.component').then(m => m.LoginComponent) },
            { path: 'register', loadComponent: () => import('../pages/register/register.component').then(m => m.RegisterComponent) },
            { path: '', redirectTo: 'login', pathMatch: 'full' }
        ]
    }
];
