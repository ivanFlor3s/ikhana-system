import { HttpClient } from "@angular/common/http";
import { Injectable, computed, inject, signal } from "@angular/core";
import { AppUser } from "@core/models/app-user.model";
import { environment } from "@environments/environment";
import { LoginDto } from "@interfaces/dtos/login.dto";
import { LoginResponseDto } from "@interfaces/dtos/response/login.response.dto";
import { take, tap } from "rxjs";


@Injectable({
    providedIn: 'root'
})
export class AuthService {

    private http = inject(HttpClient);
    private apiUrl = `${environment.apiUrl}`;

    private appUser = signal<AppUser | null>(null);

    public name = computed(() => this.appUser() ? this.appUser()!.name : '');
    public role = computed(() => this.appUser() ? this.appUser()!.role : '');

    login(credentials: LoginDto) {
        return this.http.post<LoginResponseDto>(`${this.apiUrl}/login`, credentials).pipe(
            tap(response => {
                if (response.success) {
                    localStorage.setItem('token', response.data.token);
                    this.appUser.set({
                        id: response.data.user.id,
                        name: response.data.user.name,
                        email: response.data.user.email,
                        role: response.data.user.role.name,
                    });
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
    logout() {
        return this.http.post(`${this.apiUrl}/logout`, {}).pipe(take(1)).pipe(
            tap({
                error: () => {
                    console.log('Logout failed');
                },
                complete: () => {
                    localStorage.removeItem('token');
                    this.appUser.set(null);
                }
            })
        );
    }

    /**
     * Check if the current user has the Administracion role
     */
    isAdmin = computed(() => this.role() === 'Administracion');

}