import { Component, inject, OnInit, signal } from '@angular/core';
import { RubrosHeaderComponent } from "@modules/rubros/rubros-header/rubros-header.component";
import { MatProgressSpinnerModule } from "@angular/material/progress-spinner";
import { RubrosListComponent } from "@modules/rubros/rubros-list/rubros-list.component";
import { MatDialog } from '@angular/material/dialog';
import { CategoryApiService, CategoryListResponse, PaginatedCategoryList } from '../../generated/category-api.service';
import { RubroCreateOrEditComponent } from '@modules/rubros/dialogs/rubro-create-or-edit/rubro-create-or-edit.component';

@Component({
  selector: 'app-rubros-page',
  imports: [RubrosHeaderComponent, MatProgressSpinnerModule, RubrosListComponent],
  templateUrl: './rubros-page.component.html',
  styleUrl: './rubros-page.component.css'
})
export class RubrosPageComponent implements OnInit {
  private dialog = inject(MatDialog);
  private categoryApi = inject(CategoryApiService);

  rubros = signal<CategoryListResponse[]>([]);
  isLoading = signal<boolean>(false);

  ngOnInit(): void {
    this.loadRubros();
  }

  loadRubros(): void {
    this.isLoading.set(true);

    this.categoryApi.list().subscribe({
      next: (response) => {
        this.rubros.set(response.items ?? []);
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
