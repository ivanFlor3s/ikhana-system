import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '@services/auth.service';
import { catchError, throwError } from 'rxjs';

/**
 * HTTP Interceptor that:
 * 1. Adds the authentication token to all outgoing requests
 * 2. Handles 401 Unauthorized responses by logging out and redirecting to login
 */
export const authInterceptor: HttpInterceptorFn = (req, next) => {
    const authService = inject(AuthService);
    const router = inject(Router);

    // Get the token from the auth service
    const token = authService.getToken();

    // Clone the request and add the Authorization header if token exists
    const authReq = token
        ? req.clone({
            setHeaders: {
                Authorization: `Bearer ${token}`
            }
        })
        : req;

    // Handle the request and catch any errors
    return next(authReq).pipe(
        catchError((error) => {
            // If we get a 401 Unauthorized response
            if (error.status === 401) {
                // Remove the token and redirect to login
                authService.logout();
                router.navigate(['/auth/login']);
            }

            // Re-throw the error so it can be handled by the calling code
            return throwError(() => error);
        })
    );
};
