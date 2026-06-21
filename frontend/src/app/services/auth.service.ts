import { Injectable, computed, signal } from "@angular/core";
import { AppRole, AppUser } from "@core/models/app-user.model";
import { take, tap } from "rxjs";
import { AuthApiService } from "../generated/auth-api.service";
import type { LoginRequest } from "../generated/auth-api.service";

const APP_USER_KEY = 'app-user';

@Injectable({
    providedIn: 'root'
})
export class AuthService {

    constructor(private authApi: AuthApiService) {}

    private appUser = signal<AppUser | null>(null);

    public name = computed(() => this.appUser() ? this.appUser()?.name : JSON.parse(localStorage.getItem(APP_USER_KEY) || '{}').name);
    public role = computed(() => this.appUser() ? this.appUser()?.role : JSON.parse(localStorage.getItem(APP_USER_KEY) || '{}').role);

    login(credentials: LoginRequest) {
        return this.authApi.login(credentials).pipe(
            tap(res => {
                localStorage.setItem('token', res.token);
                const user: AppUser = {
                    id: Number(res.userId),
                    name: res.name,
                    email: res.email,
                    role: (res.role as AppRole) ?? 'Operador',
                };
                this.appUser.set(user);
                localStorage.setItem(APP_USER_KEY, JSON.stringify(user));
            })
        );
    }

    isAuthenticated(): boolean {
        return !!this.getToken();
    }

    getToken(): string | null {
        return localStorage.getItem('token');
    }

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem(APP_USER_KEY);
        this.appUser.set(null);
        return this.authApi.logout().pipe(take(1)).pipe(
            tap({
                error: () => {
                    console.log('Logout failed');
                }
            })
        );
    }

    isAdmin = computed(() => this.role() === 'Administracion');
}
