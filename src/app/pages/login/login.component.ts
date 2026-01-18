import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { Component, inject, signal } from '@angular/core';
import { Button } from "@shared/components/button/button";
import { AuthService } from '@services/auth.service';
import { LoginDto } from '@interfaces/dtos/login.dto';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
import { CircleAlert, LucideAngularModule } from "lucide-angular";

@Component({
  selector: 'app-login',
  imports: [FormsModule, MatFormFieldModule, MatInputModule, Button, ReactiveFormsModule, CommonModule, LucideAngularModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css',
  host: { 'class': ' w-full ' }
})
export class LoginComponent {

  private _authService = inject(AuthService);
  private _fb = inject(FormBuilder);
  private _router = inject(Router);

  readonly CircleAlertIcon = CircleAlert;


  error = signal<string | null>(null);

  loginForm = this._fb.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required]],
  });


  onSubmit() {
    this.loginForm.markAllAsTouched();
    if (this.loginForm.valid) {
      this._authService.login(this.loginForm.value as LoginDto).subscribe({
        next: (response) => {
          this._router.navigate(['/app']);
        },
        error: (error) => {
          this.error.set("Usuario o contraseña incorrectos");
        }
      });
    }
  }

}
