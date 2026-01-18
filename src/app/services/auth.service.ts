import { HttpClient } from "@angular/common/http";
import { Injectable, inject } from "@angular/core";
import { LoginDto } from "@interfaces/dtos/login.dto";
import { LoginResponseDto } from "@interfaces/dtos/response/login.response.dto";
import { tap } from "rxjs";


@Injectable({
    providedIn: 'root'
})
export class AuthService {

    private http = inject(HttpClient);

    login(credentials: LoginDto) {
        return this.http.post<LoginResponseDto>('/api/auth/login', credentials).pipe(
            tap(response => {
                if (response.success) {
                    localStorage.setItem('token', response.data.token);
                }
            })
        );
    }
}