import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { FormArray, FormBuilder, FormControl, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatIconModule } from '@angular/material/icon';

import {
  // MAT_DIALOG_DATA,
  MatDialogActions,
  MatDialogClose,
  MatDialogContent,
  MatDialogRef,
  MatDialogTitle,
} from '@angular/material/dialog';
import { CommonModule } from '@angular/common';
import { Button } from '@shared/components/button/button';
import { AfipService } from '@services/afip.service';
import { cuilAsyncValidator } from '@validators/cuil-validator';
import { AppInitService } from '@services/app-init.service';
import { ProviderService } from '@services/provider.service';
import { NotificationService } from '@services/notification.service';
import { mapProviderFormToDto } from '@interfaces/mappers/provider-form.mapper';
import { ProviderFormData } from '@interfaces/form-data-models/provider-form-data.model';

@Component({
  selector: 'app-provider-create-or-edit',
  imports: [MatFormFieldModule,
    MatInputModule,
    FormsModule,
    ReactiveFormsModule,
    MatSelectModule,
    CommonModule,
    MatButtonModule,
    MatDialogTitle,
    MatDialogContent,
    MatDialogActions,
    MatProgressSpinnerModule,
    MatIconModule,
    Button],
  templateUrl: './provider-create-or-edit.html',
  styleUrl: './provider-create-or-edit.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class ProviderCreateOrEdit {

  readonly dialogRef = inject(MatDialogRef<ProviderCreateOrEdit>);
  // readonly data = inject<DialogData>(MAT_DIALOG_DATA);


  fb = inject(FormBuilder);
  afipService = inject(AfipService);
  appInitService = inject(AppInitService);
  providerService = inject(ProviderService);
  notificationService = inject(NotificationService);

  isSubmitting = signal(false);
  errorMessage = signal<string | null>(null);

  form = this.fb.group({
    name: ['', Validators.required],
    cuit: ['', {
      validators: [Validators.required],
      asyncValidators: [cuilAsyncValidator(this.afipService)],
      updateOn: 'blur' // Trigger validation on blur (when user exits the field)
    }],
    iib: [''],
    address: [''],
    socialReason: [''],
    ivaPositionId: [null as number | null, Validators.required],
    convenioId: [null as number | null, Validators.required],
    categoryId: [null as number | null, Validators.required],
    website: [''],

    phone: ['', Validators.required],
    otherPhones: this.fb.array<string>([]),

    email: ['', [Validators.required, Validators.email]],
    otherEmails: this.fb.array<string>([]),

    observations: [''],

    since: this.fb.group({
      hours: [8, Validators.required],
      minutes: [0, Validators.required]
    }),

    to: this.fb.group({
      hours: [17, Validators.required],
      minutes: [0, Validators.required]
    }),

    // Broker fields
    brokerFirstName: ['', Validators.required],
    brokerLastName: ['', Validators.required],
    brokerEmail: ['', [Validators.required, Validators.email]],
    brokerPhone: ['', Validators.required],
  });

  // Getters for reference data
  get taxStatuses() {
    return this.appInitService.taxStatuses;
  }

  get agreements() {
    return this.appInitService.agreements;
  }

  get categories() {
    return this.appInitService.categories;
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  // Helpers para arrays
  get otherPhones(): FormArray<FormControl<string | null>> {
    return this.form.get('otherPhones') as FormArray<FormControl<string | null>>;
  }

  get cuitControl(): FormControl {
    return this.form.get('cuit') as FormControl;
  }

  get otherEmails(): FormArray<FormControl<string | null>> {
    return this.form.get('otherEmails') as FormArray<FormControl<string | null>>;
  }

  addPhone() {
    const f = this.fb.control<string>('', Validators.required);
    this.otherPhones.push(f);
  }

  removePhone(i: number) {
    this.otherPhones.removeAt(i);
  }

  addEmail() {
    this.otherEmails.push(
      this.fb.control('', [Validators.required, Validators.email])
    );
  }

  removeEmail(i: number) {
    this.otherEmails.removeAt(i);
  }

  submit() {
    if (this.form.valid) {
      this.isSubmitting.set(true);
      this.errorMessage.set(null);

      const formData = this.form.value as ProviderFormData;
      const dto = mapProviderFormToDto(formData);

      this.providerService.createProvider(dto).subscribe({
        next: (response) => {
          this.isSubmitting.set(false);
          this.notificationService.success(
            'Proveedor creado exitosamente',
            `El proveedor "${response.data.fantasy_name}" ha sido creado correctamente.`
          );
          this.dialogRef.close(response.data);
        },
        error: (error) => {
          this.isSubmitting.set(false);
          this.errorMessage.set(error.error?.message || 'Error al crear el proveedor');
          console.error('Error creating provider:', error);
        }
      });
    } else {
      this.form.markAllAsTouched();
    }
  }

}
