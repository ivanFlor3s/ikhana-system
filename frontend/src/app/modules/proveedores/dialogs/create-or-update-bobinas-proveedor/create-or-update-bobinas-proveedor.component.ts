import { Component, computed, inject, signal } from '@angular/core';
import { FormBuilder, Validators, ReactiveFormsModule, FormsModule } from '@angular/forms';
import { MatDialogContent, MatDialogActions, MAT_DIALOG_DATA, MatDialogRef, MatDialogTitle } from "@angular/material/dialog";
import { MatFormField, MatInputModule, MatLabel } from "@angular/material/input";
import { NameValue } from '@models/name-value.model';
import { Provider } from '@models/provider.model';
import { ProviderService } from '@services/provider.service';
import { NotificationService } from '@services/notification.service';
import { take } from 'rxjs';
import { MatOption, MatSelect, MatSelectModule } from "@angular/material/select";
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatButtonModule } from '@angular/material/button';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { CommonModule } from '@angular/common';

interface BobinasProveedorDialogData {
  providerId: number | null;
  providerName?: string;
  currentCoils?: number;
}

@Component({
  selector: 'app-create-or-update-bobinas-proveedor',
  imports: [
    MatDialogContent,
    MatFormFieldModule,
    MatDialogActions,
    MatSelectModule,
    ReactiveFormsModule,
    MatInputModule,
    MatButtonModule,
    MatDialogTitle,
    FormsModule,
    CommonModule,
    MatProgressSpinnerModule,
  ],
  templateUrl: './create-or-update-bobinas-proveedor.component.html',
  styleUrl: './create-or-update-bobinas-proveedor.component.css'
})
export class CreateOrUpdateBobinasProveedorComponent {

  readonly dialogRef = inject(MatDialogRef<CreateOrUpdateBobinasProveedorComponent>);
  readonly data = inject<BobinasProveedorDialogData>(MAT_DIALOG_DATA, { optional: true });
  private fb = inject(FormBuilder);
  private providerService = inject(ProviderService);
  private notificationService = inject(NotificationService);

  providers = signal<NameValue<number>[]>([]);
  loadingProviders = signal<boolean>(false);
  isSubmitting = signal<boolean>(false);
  errorMessage = signal<string | null>(null);

  isCreating = computed(() => this.data?.providerId === null);

  form = this.fb.group({
    providerId: [{ value: 0, disabled: !this.isCreating() }, Validators.required],
    coilAmount: [0, [Validators.required, Validators.min(0)]],
  });

  ngOnInit(): void {
    if (this.isCreating()) {
      this.loadProviders();
    } else if (this.data?.providerId) {
      this.form.patchValue({
        providerId: this.data.providerId,
        coilAmount: this.data.currentCoils ?? 0,
      });
      this.form.get('providerId')?.disable();
    }
  }

  private loadProviders(): void {
    this.loadingProviders.set(true);
    this.providerService.getProvidersSummary().pipe(take(1)).subscribe({
      next: (response) => {
        this.providers.set(response.data
          .map((provider: Provider) => ({ name: provider.fantasy_name, value: provider.id }))
        );
      },
      complete: () => {
        this.loadingProviders.set(false);
      }
    });
  }

  submit() {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting.set(true);
    this.errorMessage.set(null);

    const providerId = this.form.getRawValue().providerId!;
    const coilAmount = this.form.getRawValue().coilAmount!;

    const operation = this.isCreating()
      ? this.providerService.createProviderInventoryCoils(providerId, coilAmount)
      : this.providerService.updateProviderInventoryCoils(providerId, coilAmount);

    operation.pipe(take(1)).subscribe({
      next: () => {
        this.isSubmitting.set(false);
        const action = this.isCreating() ? 'creado' : 'actualizado';
        this.notificationService.success(
          `Registro ${action} exitosamente`,
          `El registro de bobinas ha sido ${action} correctamente.`
        );
        this.dialogRef.close(true);
      },
      error: (error) => {
        this.isSubmitting.set(false);
        const message = error.error?.message || `Error al ${this.isCreating() ? 'crear' : 'actualizar'} el registro de bobinas`;
        this.notificationService.error('Error', message);
      }
    });
  }
}
