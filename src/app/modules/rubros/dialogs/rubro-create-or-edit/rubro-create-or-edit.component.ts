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
import { RubroService } from '@services/rubro.service';
import { NotificationService } from '@services/notification.service';
import { CreateRubroDto } from '@interfaces/dtos/create-rubro.dto';
import { Rubro } from '@models/rubro';

interface DialogData {
  rubro?: Rubro
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
  private rubroService = inject(RubroService);
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
      this.loadRubro(this.data.rubro.id);
    }
  }

  private loadRubro(id: number): void {
    this.isLoading.set(true);
    this.rubroService.getRubroById(id).subscribe({
      next: (response) => {
        if (response.success) {
          this.form.patchValue({
            name: response.data.name,
            description: response.data.description
          });
        }
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

      const dto: CreateRubroDto = {
        name: this.form.value.name!,
        description: this.form.value.description || undefined
      };

      const operation = this.isEditMode && this.data?.rubro?.id
        ? this.rubroService.updateRubro(this.data.rubro.id, dto)
        : this.rubroService.createRubro(dto);

      operation.subscribe({
        next: (response) => {
          this.isSubmitting.set(false);
          const action = this.isEditMode ? 'actualizado' : 'creado';
          this.notificationService.success(
            `Rubro ${action} exitosamente`,
            `El rubro "${response.data.name}" ha sido ${action} correctamente.`
          );
          this.dialogRef.close(response.data);
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
