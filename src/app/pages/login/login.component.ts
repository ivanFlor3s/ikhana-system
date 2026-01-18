import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';
import { FormsModule } from '@angular/forms';
import { Component } from '@angular/core';
import { Button } from "@shared/components/button/button";

@Component({
  selector: 'app-login',
  imports: [FormsModule, MatFormFieldModule, MatInputModule, Button],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css',
  host: { 'class': ' w-full ' }
})
export class LoginComponent {

}
