import { HttpClient } from "@angular/common/http";
import { Injectable, inject } from "@angular/core";
import { environment } from "@environments/environment";
import { LoginDto } from "@interfaces/dtos/login.dto";
import { LoginResponseDto } from "@interfaces/dtos/response/login.response.dto";
import { tap } from "rxjs";


@Injectable({
    providedIn: 'root'
})
export class AuthService {

    private http = inject(HttpClient);
    private apiUrl = `${environment.apiUrl}/login`;

    login(credentials: LoginDto) {
        return this.http.post<LoginResponseDto>(this.apiUrl, credentials).pipe(
            tap(response => {
                if (response.success) {
                    localStorage.setItem('token', response.data.token);
                }
            })
        );
    }

    /**
     * Check if user is authenticated by verifying token exists in localStorage
     */
    isAuthenticated(): boolean {
        return !!this.getToken();
    }

    /**
     * Get the authentication token from localStorage
     */
    getToken(): string | null {
        return localStorage.getItem('token');
    }

    /**
     * Logout user by removing token from localStorage
     */
    logout(): void {
        localStorage.removeItem('token');
    }
}