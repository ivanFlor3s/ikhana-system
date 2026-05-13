import { Component, inject, OnInit, signal } from '@angular/core';
import { RubrosHeaderComponent } from "@modules/rubros/rubros-header/rubros-header.component";
import { MatProgressSpinnerModule } from "@angular/material/progress-spinner";
import { RubrosListComponent } from "@modules/rubros/rubros-list/rubros-list.component";
import { MatDialog } from '@angular/material/dialog';
import { RubroService } from '../../services/rubro.service';
import { Rubro } from '../../models/rubro.model';
import { RubroCreateOrEditComponent } from '@modules/rubros/dialogs/rubro-create-or-edit/rubro-create-or-edit.component';

@Component({
  selector: 'app-rubros-page',
  imports: [RubrosHeaderComponent, MatProgressSpinnerModule, RubrosListComponent],
  templateUrl: './rubros-page.component.html',
  styleUrl: './rubros-page.component.css'
})
export class RubrosPageComponent implements OnInit {
  private dialog = inject(MatDialog);
  private rubroService = inject(RubroService);

  // State signals
  rubros = signal<Rubro[]>([]);
  isLoading = signal<boolean>(false);

  ngOnInit(): void {
    this.loadRubros();
  }

  loadRubros(): void {
    this.isLoading.set(true);

    this.rubroService.getRubros().subscribe({
      next: (response) => {
        if (response.success) {
          this.rubros.set(response.data);
        }
        this.isLoading.set(false);
      },
      error: (error) => {
        console.error('Error loading rubros:', error);
        this.isLoading.set(false);
      }
    });
  }

  openRubroDialog(rubroId?: number): void {
    const dialogRef = this.dialog.open(RubroCreateOrEditComponent, {
      width: '600px',
      data: {
        rubroId: rubroId,
        mode: rubroId ? 'edit' : 'create'
      }
    });

    dialogRef.afterClosed().subscribe((result) => {
      if (result) {
        // Reload rubros list after create/edit
        this.loadRubros();
      }
    });
  }

  onRubroDeleted(): void {
    // Reload the rubros list after successful deletion
    this.loadRubros();
  }
}
