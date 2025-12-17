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
import { BrokerService } from '@services/broker.service';
import { NotificationService } from '@services/notification.service';
import { mapProviderFormToDto } from '@interfaces/mappers/provider-form.mapper';
import { ProviderFormData } from '@interfaces/form-data-models/provider-form-data.model';
import { Provider } from '@models/provider.model';
import { CreateBrokerDto } from '@interfaces/dtos/create-broker.dto';
import { switchMap, catchError } from 'rxjs/operators';
import { of, throwError } from 'rxjs';

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
    Button],
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
  providerService = inject(ProviderService);
  brokerService = inject(BrokerService);
  notificationService = inject(NotificationService);

  isSubmitting = signal(false);
  isCreatingBroker = signal(false);
  isLoading = signal(false);
  errorMessage = signal<string | null>(null);
  isEditMode = computed(() => !!this.data?.providerId);
  isBrokerFormExpanded = signal(false);

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

    // Broker fields (initially not required)
    brokerFirstName: [''],
    brokerLastName: [''],
    brokerEmail: [''],
    brokerPhone: [''],
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

  ngOnInit(): void {
    if (this.isEditMode() && this.data?.providerId) {
      this.isLoading.set(true);
      this.providerService.getProviderById(this.data.providerId).subscribe({
        next: (response) => {
          this.isLoading.set(false);
          this.populateForm(response.data);
        },
        error: (error) => {
          this.isLoading.set(false);
          this.errorMessage.set('Error al cargar los datos del proveedor');
          console.error('Error fetching provider:', error);
        }
      });
    }
  }

  populateForm(provider: Provider): void {
    // Parse business hours
    const parseTime = (timeStr: string) => {
      const [hours, minutes] = timeStr.split(':').map(Number);
      return { hours, minutes };
    };

    const since = parseTime(provider.business_hours_start);
    const to = parseTime(provider.business_hours_end);

    // Collect additional phones and emails
    const otherPhones = [provider.phone_2, provider.phone_3, provider.phone_4, provider.phone_5]
      .filter(phone => phone !== null && phone !== '');
    const otherEmails = [provider.email_2, provider.email_3, provider.email_4, provider.email_5]
      .filter(email => email !== null && email !== '');

    // Populate form
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
      brokerFirstName: '',
      brokerLastName: '',
      brokerEmail: '',
      brokerPhone: '',
    });

    // Add additional phones
    otherPhones.forEach(phone => {
      this.otherPhones.push(this.fb.control(phone, Validators.required));
    });

    // Add additional emails
    otherEmails.forEach(email => {
      this.otherEmails.push(this.fb.control(email, [Validators.required, Validators.email]));
    });
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

  toggleBrokerForm() {
    this.isBrokerFormExpanded.update(expanded => !expanded);
    this.updateBrokerFieldValidators();
  }

  updateBrokerFieldValidators() {
    const brokerFields = ['brokerFirstName', 'brokerLastName', 'brokerEmail', 'brokerPhone'];

    if (this.isBrokerFormExpanded()) {
      // Add validators when expanded
      this.form.get('brokerFirstName')?.setValidators([Validators.required]);
      this.form.get('brokerLastName')?.setValidators([Validators.required]);
      this.form.get('brokerEmail')?.setValidators([Validators.required, Validators.email]);
      this.form.get('brokerPhone')?.setValidators([Validators.required]);
    } else {
      // Remove validators when collapsed
      brokerFields.forEach(field => {
        this.form.get(field)?.clearValidators();
        this.form.get(field)?.setValue('');
      });
    }

    // Update validity
    brokerFields.forEach(field => this.form.get(field)?.updateValueAndValidity());
  }

  submit() {
    if (this.form.valid) {
      this.isSubmitting.set(true);
      this.errorMessage.set(null);

      const formData = this.form.value as ProviderFormData;

      if (this.isEditMode() && this.data?.providerId) {
        // Edit mode - for now just show success message without API call
        this.isSubmitting.set(false);
        this.notificationService.success(
          'Proveedor actualizado',
          `El proveedor "${formData.name}" será actualizado cuando el endpoint esté listo.`
        );
        this.dialogRef.close({ updated: true, providerId: this.data.providerId });
      } else {
        // Create mode - check if we need to create broker first
        if (this.isBrokerFormExpanded()) {
          this.createBrokerAndProvider(formData);
        } else {
          this.createProvider(formData, null);
        }
      }
    } else {
      this.form.markAllAsTouched();
    }
  }

  createBrokerAndProvider(formData: ProviderFormData) {
    this.isCreatingBroker.set(true);

    const brokerDto: CreateBrokerDto = {
      first_name: formData.brokerFirstName!,
      last_name: formData.brokerLastName!,
      email: formData.brokerEmail!,
      phone: formData.brokerPhone!,
    };

    this.brokerService.createBroker(brokerDto).pipe(
      switchMap((brokerResponse) => {
        this.isCreatingBroker.set(false);
        // Now create provider with broker_id
        return this.createProviderObservable(formData, brokerResponse.data.id);
      }),
      catchError((error) => {
        this.isCreatingBroker.set(false);
        this.isSubmitting.set(false);
        this.errorMessage.set(error.error?.message || 'Error al crear el corredor');
        console.error('Error creating broker:', error);
        return throwError(() => error);
      })
    ).subscribe({
      next: (response) => {
        this.isSubmitting.set(false);
        this.notificationService.success(
          'Proveedor y corredor creados exitosamente',
          `El proveedor "${response.data.fantasy_name}" y su corredor han sido creados correctamente.`
        );
        this.dialogRef.close(response.data);
      },
      error: (error) => {
        this.isSubmitting.set(false);
        this.errorMessage.set(error.error?.message || 'Error al crear el proveedor');
        console.error('Error creating provider:', error);
      }
    });
  }

  createProvider(formData: ProviderFormData, brokerId: number | null) {
    this.createProviderObservable(formData, brokerId).subscribe({
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
  }

  createProviderObservable(formData: ProviderFormData, brokerId: number | null) {
    const dto = mapProviderFormToDto(formData);
    // Add broker_id if provided
    if (brokerId !== null) {
      dto.broker_id = brokerId;
    }
    return this.providerService.createProvider(dto);
  }

}
