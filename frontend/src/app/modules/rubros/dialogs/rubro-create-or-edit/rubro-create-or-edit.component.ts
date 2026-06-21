import { Component, inject, signal, OnInit } from '@angular/core';
import { FormBuilder, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import {
  MAT_DIALOG_DATA,
  MatDialogActions,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle,
} from '@angular/material/dialog';
import { CommonModule } from '@angular/common';
import { Button } from '@shared/components/button/button';
import { CategoryApiService, CategoryDetailResponse, CreateCategoryCommand, UpdateCategoryCommand, CategoryListResponse } from '@generated/category-api.service';
import { NotificationService } from '@services/notification.service';

interface DialogData {
  rubro?: CategoryListResponse;
  mode: 'create' | 'edit';
}

@Component({
  selector: 'app-rubro-create-or-edit',
  imports: [
    MatFormFieldModule,
    MatInputModule,
    FormsModule,
    ReactiveFormsModule,
    CommonModule,
    MatButtonModule,
    MatDialogTitle,
    MatDialogContent,
    MatDialogActions,
    MatProgressSpinnerModule,
    Button
  ],
  templateUrl: './rubro-create-or-edit.component.html',
  styleUrl: './rubro-create-or-edit.component.css'
})
export class RubroCreateOrEditComponent implements OnInit {
  readonly dialogRef = inject(MatDialogRef<RubroCreateOrEditComponent>);
  readonly data = inject<DialogData>(MAT_DIALOG_DATA, { optional: true });

  private fb = inject(FormBuilder);
  private categoryApi = inject(CategoryApiService);
  private notificationService = inject(NotificationService);

  isSubmitting = signal(false);
  isLoading = signal(false);
  errorMessage = signal<string | null>(null);

  form = this.fb.group({
    name: ['', Validators.required],
    description: ['']
  });

  get isEditMode(): boolean {
    return this.data?.mode === 'edit' && !!this.data?.rubro?.id;
  }

  get dialogTitle(): string {
    return this.isEditMode ? 'Editar Rubro' : 'Crear Rubro';
  }

  ngOnInit(): void {
    if (this.isEditMode && this.data?.rubro?.id) {
      this.loadRubro(Number(this.data.rubro.id));
    }
  }

  private loadRubro(id: number): void {
    this.isLoading.set(true);
    this.categoryApi.get(id).subscribe({
      next: (rubro) => {
        this.form.patchValue({
          name: rubro.name,
          description: rubro.description ?? ''
        });
        this.isLoading.set(false);
      },
      error: (error) => {
        console.error('Error loading rubro:', error);
        this.errorMessage.set('Error al cargar el rubro');
        this.isLoading.set(false);
      }
    });
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  submit(): void {
    if (this.form.valid) {
      this.isSubmitting.set(true);
      this.errorMessage.set(null);

      const command: CreateCategoryCommand = {
        name: this.form.value.name!,
        description: this.form.value.description ?? null
      };

      const operation = this.isEditMode && this.data?.rubro?.id
        ? this.categoryApi.update(Number(this.data.rubro.id), {
            id: Number(this.data.rubro.id),
            name: command.name,
            description: command.description
          } as UpdateCategoryCommand)
        : this.categoryApi.create(command);

      operation.subscribe({
        next: (response: CategoryDetailResponse) => {
          this.isSubmitting.set(false);
          const action = this.isEditMode ? 'actualizado' : 'creado';
          this.notificationService.success(
            `Rubro ${action} exitosamente`,
            `El rubro "${response.name}" ha sido ${action} correctamente.`
          );
          this.dialogRef.close(response);
        },
        error: (error) => {
          this.isSubmitting.set(false);
          this.errorMessage.set(error.error?.message || `Error al ${this.isEditMode ? 'actualizar' : 'crear'} el rubro`);
          console.error('Error submitting rubro:', error);
        }
      });
    } else {
      this.form.markAllAsTouched();
    }
  }
}
