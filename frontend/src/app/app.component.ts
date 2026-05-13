import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { SnackbarContainerComponent } from './core/components/snackbar/snackbar-container.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, SnackbarContainerComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'kikhana-web';
}
