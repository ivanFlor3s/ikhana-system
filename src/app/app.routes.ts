import { Routes } from '@angular/router';
import { authRoutes } from './routes/auth.routes';
import { appRoutes } from './routes/app.route';

export const routes: Routes = [
    { path: 'auth', children: authRoutes },
    { path: 'app', children: appRoutes },
];
