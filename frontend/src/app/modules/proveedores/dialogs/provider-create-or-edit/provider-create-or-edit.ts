import { ChangeDetectionStrategy, Component, computed, inject, OnInit, signal } from '@angular/core';
import { FormArray, FormBuilder, FormControl, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { MatButtonModule } from '@angular/material/button';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatIconModule } from '@angular/material/icon';

import {
  MAT_DIALOG_DATA,
  MatDialogActions,
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
import { AuthService } from '@services/auth.service';
import { mapProviderFormToDto } from '@interfaces/mappers/provider-form.mapper';
import { ProviderFormData } from '@interfaces/form-data-models/provider-form-data.model';
import { LucideAngularModule } from 'lucide-angular';
import { ProviderApiService } from '@generated/provider-api.service';

interface DialogData {
  providerId?: number;
}

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
    Button, LucideAngularModule],
  templateUrl: './provider-create-or-edit.html',
  styleUrl: './provider-create-or-edit.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class ProviderCreateOrEdit implements OnInit {

  readonly dialogRef = inject(MatDialogRef<ProviderCreateOrEdit>);
  readonly data = inject<DialogData>(MAT_DIALOG_DATA, { optional: true });

  fb = inject(FormBuilder);
  afipService = inject(AfipService);
  appInitService = inject(AppInitService);
  providerService = inject(ProviderApiService);
  notificationService = inject(NotificationService);
  authService = inject(AuthService);

  isSubmitting = signal(false);
  isLoading = signal(false);
  errorMessage = signal<string | null>(null);
  isEditMode = computed(() => !!this.data?.providerId);
  isAdmin = signal(this.authService.isAdmin());
  originalProviderData = signal<ProviderFormData | null>(null);

  form = this.fb.group({
    name: ['', Validators.required],
    cuit: ['', {
      validators: [Validators.required],
      asyncValidators: [cuilAsyncValidator(this.afipService)],
      updateOn: 'blur'
    }],
    iib: [''],
    address: [''],
    socialReason: [''],
    ivaPositionId: [null as number | null, Validators.required],
    convenioId: [null as number | null, Validators.required],
    categoryId: [null as number | null, Validators.required],
    website: [''],

    phone: [''],
    otherPhones: this.fb.array<string>([]),

    email: [''],
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
  });

  get taxStatuses() { return this.appInitService.taxStatuses; }
  get agreements() { return this.appInitService.agreements; }
  get categories() { return this.appInitService.categories; }

  ngOnInit(): void {
    if (!this.isAdmin()) {
      this.form.get('cuit')?.clearValidators();
      this.form.get('cuit')?.clearAsyncValidators();
      this.form.get('cuit')?.updateValueAndValidity();
      this.form.get('ivaPositionId')?.clearValidators();
      this.form.get('ivaPositionId')?.updateValueAndValidity();
      this.form.get('convenioId')?.clearValidators();
      this.form.get('convenioId')?.updateValueAndValidity();
    }

    if (this.isEditMode() && this.data?.providerId) {
      this.isLoading.set(true);
      // this.providerService.getProviderById(this.data.providerId).subscribe({
      //   next: (response) => {
      //     this.populateForm(response.data);
      //     this.isLoading.set(false);
      //   },
      //   error: (error) => {
      //     this.isLoading.set(false);
      //     this.errorMessage.set('Error al cargar los datos del proveedor');
      //     console.error('Error fetching provider:', error);
      //   }
      // });
    }
  }

  populateForm(provider: any): void {
    const parseTime = (timeStr: string) => {
      const [hours, minutes] = timeStr.split(':').map(Number);
      return { hours, minutes };
    };

    const since = parseTime(provider.business_hours_start);
    const to = parseTime(provider.business_hours_end);

    const otherPhones = [provider.phone_2, provider.phone_3, provider.phone_4, provider.phone_5]
      .filter((phone: string) => phone !== null && phone !== '');
    const otherEmails = [provider.email_2, provider.email_3, provider.email_4, provider.email_5]
      .filter((email: string) => email !== null && email !== '');

    this.form.patchValue({
      name: provider.fantasy_name,
      cuit: provider.cuit,
      iib: provider.iibb,
      address: provider.address,
      socialReason: provider.business_name,
      ivaPositionId: provider.tax_status_id,
      convenioId: provider.agreement_id,
      categoryId: provider.category_id,
      website: provider.website,
      phone: provider.phone_1,
      email: provider.email_1,
      observations: provider.observations,
      since,
      to,
    });

    otherPhones.forEach((phone: string) => {
      this.otherPhones.push(this.fb.control(phone, Validators.required));
    });

    otherEmails.forEach((email: string) => {
      this.otherEmails.push(this.fb.control(email, [Validators.required, Validators.email]));
    });

    this.originalProviderData.set({ ...this.form.value } as ProviderFormData);
  }

  hasProviderChanges(currentData: ProviderFormData): boolean {
    const original = this.originalProviderData();
    if (!original) return true;

    return (
      original.name !== currentData.name ||
      original.cuit !== currentData.cuit ||
      original.iib !== currentData.iib ||
      original.address !== currentData.address ||
      original.socialReason !== currentData.socialReason ||
      original.ivaPositionId !== currentData.ivaPositionId ||
      original.convenioId !== currentData.convenioId ||
      original.categoryId !== currentData.categoryId ||
      original.website !== currentData.website ||
      original.phone !== currentData.phone ||
      original.email !== currentData.email ||
      original.observations !== currentData.observations ||
      original.since?.hours !== currentData.since?.hours ||
      original.since?.minutes !== currentData.since?.minutes ||
      original.to?.hours !== currentData.to?.hours ||
      original.to?.minutes !== currentData.to?.minutes ||
      JSON.stringify(original.otherPhones) !== JSON.stringify(currentData.otherPhones) ||
      JSON.stringify(original.otherEmails) !== JSON.stringify(currentData.otherEmails)
    );
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  get otherPhones(): FormArray<FormControl<string | null>> {
    return this.form.get('otherPhones') as FormArray<FormControl<string | null>>;
  }

  get cuitControl(): FormControl {
    return this.form.get('cuit') as FormControl;
  }

  get nameControl(): FormControl {
    return this.form.get('name') as FormControl;
  }

  get otherEmails(): FormArray<FormControl<string | null>> {
    return this.form.get('otherEmails') as FormArray<FormControl<string | null>>;
  }

  addPhone() {
    this.otherPhones.push(this.fb.control('', Validators.required));
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
    if (!this.form.valid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting.set(true);
    this.errorMessage.set(null);

    const formData = this.form.value as ProviderFormData;

    if (this.isEditMode() && this.data?.providerId) {
      if (!this.hasProviderChanges(formData)) {
        this.isSubmitting.set(false);
        this.notificationService.success('Sin cambios', 'No se realizaron cambios en el proveedor.');
        this.dialogRef.close();
        return;
      }
      this.updateProvider(this.data.providerId, formData);
    } else {
      this.createProvider(formData);
    }
  }

  updateProvider(providerId: number, formData: ProviderFormData) {
    const dto = mapProviderFormToDto(formData);
    // this.providerService.updateProvider(providerId, dto).subscribe({
    //   next: (response) => {
    //     this.isSubmitting.set(false);
    //     this.notificationService.success(
    //       'Proveedor actualizado',
    //       `El proveedor "${response.data.fantasy_name}" ha sido actualizado correctamente.`
    //     );
    //     this.dialogRef.close(response.data);
    //   },
    //   error: (error) => {
    //     this.isSubmitting.set(false);
    //     this.errorMessage.set(error.error?.message || 'Error al actualizar el proveedor');
    //     console.error('Error updating provider:', error);
    //   }
    // });
  }

  createProvider(formData: ProviderFormData) {
    const dto = mapProviderFormToDto(formData);
    this.providerService.create(dto).subscribe({
      next: (response) => {
        this.isSubmitting.set(false);
        this.notificationService.success(
          'Proveedor creado exitosamente',
          `El proveedor "${response.fantasyName}" ha sido creado correctamente.`
        );
        this.dialogRef.close(response);
      },
      error: (error) => {
        this.isSubmitting.set(false);
        this.errorMessage.set(error.error?.message || 'Error al crear el proveedor');
        console.error('Error creating provider:', error);
      }
    });
  }
}
