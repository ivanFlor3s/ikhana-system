import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '@services/auth.service';

/**
 * Guest guard that prevents authenticated users from accessing auth pages.
 * Redirects to the app if user is already logged in.
 */
export const guestGuard: CanActivateFn = (route, state) => {
    const authService = inject(AuthService);
    const router = inject(Router);

    // If user is authenticated, redirect to app
    if (authService.isAuthenticated()) {
        return router.createUrlTree(['/app']);
    }

    // Allow access to auth pages if not authenticated
    return true;
};
