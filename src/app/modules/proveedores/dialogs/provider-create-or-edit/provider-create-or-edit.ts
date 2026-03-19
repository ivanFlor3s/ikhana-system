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
import { AuthService } from '@services/auth.service';
import { mapProviderFormToDto } from '@interfaces/mappers/provider-form.mapper';
import { ProviderFormData } from '@interfaces/form-data-models/provider-form-data.model';
import { Provider, Broker } from '@models/provider.model';
import { CreateBrokerDto } from '@interfaces/dtos/create-broker.dto';
import { switchMap, catchError } from 'rxjs/operators';
import { of, throwError, forkJoin } from 'rxjs';
import { Trash2, LucideAngularModule, ChevronDown, ChevronRight } from 'lucide-angular';
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
  readonly TrashIcon = Trash2;
  readonly ChevronDownIcon = ChevronDown;
  readonly ChevronRightIcon = ChevronRight;


  fb = inject(FormBuilder);
  afipService = inject(AfipService);
  appInitService = inject(AppInitService);
  providerService = inject(ProviderService);
  brokerService = inject(BrokerService);
  notificationService = inject(NotificationService);
  authService = inject(AuthService);

  isSubmitting = signal(false);
  isCreatingBroker = signal(false);
  isLoading = signal(false);
  errorMessage = signal<string | null>(null);
  isEditMode = computed(() => !!this.data?.providerId);
  isAdmin = signal(this.authService.isAdmin());
  isBrokerFormExpanded = signal(false);
  existingBrokerId = signal<number | null>(null);
  brokerToDelete = signal(false);
  originalProviderData = signal<ProviderFormData | null>(null);

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

  // Signal-based getters for reference data
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
    // Remove validators from admin-only fields for non-admin users
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
      this.providerService.getProviderById(this.data.providerId).subscribe({
        next: (response) => {
          this.populateForm(response.data);
          // If provider has a broker, fetch broker details
          if (response.data.broker_id) {
            this.existingBrokerId.set(response.data.broker_id);
            this.brokerService.getBrokerById(response.data.broker_id).subscribe({
              next: (brokerResponse) => {
                this.populateBrokerForm(brokerResponse.data);
                this.isBrokerFormExpanded.set(true);
                this.updateBrokerFieldValidators();
                this.isLoading.set(false);
              },
              error: (error) => {
                this.isLoading.set(false);
                console.error('Error fetching broker:', error);
                // Continue without broker data
              }
            });
          } else {
            this.isLoading.set(false);
          }
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
    });

    // Add additional phones
    otherPhones.forEach(phone => {
      this.otherPhones.push(this.fb.control(phone, Validators.required));
    });

    // Add additional emails
    otherEmails.forEach(email => {
      this.otherEmails.push(this.fb.control(email, [Validators.required, Validators.email]));
    });

    // Store original provider data for change detection
    this.originalProviderData.set({
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
      otherPhones: otherPhones as string[],
      otherEmails: otherEmails as string[],
      brokerFirstName: '',
      brokerLastName: '',
      brokerEmail: '',
      brokerPhone: '',
    });
  }

  hasProviderChanges(currentData: ProviderFormData): boolean {
    const original = this.originalProviderData();
    if (!original) return true; // If no original data, consider it changed

    // Compare provider fields (excluding broker fields)
    const providerFieldsChanged =
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
      original.to?.minutes !== currentData.to?.minutes;

    // Compare arrays
    const otherPhonesChanged = JSON.stringify(original.otherPhones) !== JSON.stringify(currentData.otherPhones);
    const otherEmailsChanged = JSON.stringify(original.otherEmails) !== JSON.stringify(currentData.otherEmails);

    return providerFieldsChanged || otherPhonesChanged || otherEmailsChanged;
  }

  populateBrokerForm(broker: Broker): void {
    this.form.patchValue({
      brokerFirstName: broker.first_name,
      brokerLastName: broker.last_name,
      brokerEmail: broker.email,
      brokerPhone: broker.phone,
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

  removeBroker() {
    if (this.existingBrokerId()) {
      this.brokerToDelete.set(true);
      this.existingBrokerId.set(null);
    }
    this.isBrokerFormExpanded.set(false);
    // Clear broker form fields
    this.form.patchValue({
      brokerFirstName: '',
      brokerLastName: '',
      brokerEmail: '',
      brokerPhone: '',
    });
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
        // Edit mode - handle broker operations
        this.handleEditMode(formData);
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

  handleEditMode(formData: ProviderFormData) {
    const providerId = this.data!.providerId!;

    // Scenario 1: Broker marked for deletion
    if (this.brokerToDelete()) {
      const brokerId = this.existingBrokerId();
      if (brokerId) {
        this.brokerService.deleteBroker(brokerId).pipe(
          switchMap(() => this.updateProviderObservable(providerId, formData, null))
        ).subscribe({
          next: (response) => {
            this.isSubmitting.set(false);
            this.notificationService.success(
              'Proveedor actualizado',
              `El proveedor "${response.data.fantasy_name}" ha sido actualizado y el corredor eliminado.`
            );
            this.dialogRef.close(response.data);
          },
          error: (error) => {
            this.isSubmitting.set(false);
            this.errorMessage.set(error.error?.message || 'Error al actualizar el proveedor');
            console.error('Error updating provider:', error);
          }
        });
      } else {
        // Just update provider without broker
        this.updateProvider(providerId, formData, null);
      }
    }
    // Scenario 2: Broker form expanded with data
    else if (this.isBrokerFormExpanded() && formData.brokerFirstName) {
      const brokerDto: CreateBrokerDto = {
        first_name: formData.brokerFirstName!,
        last_name: formData.brokerLastName!,
        email: formData.brokerEmail!,
        phone: formData.brokerPhone!,
      };

      // If existing broker, update it
      if (this.existingBrokerId()) {
        this.isCreatingBroker.set(true);
        this.brokerService.updateBroker(this.existingBrokerId()!, brokerDto).pipe(
          switchMap((brokerResponse) => {
            this.isCreatingBroker.set(false);
            // Only update provider if provider data changed
            if (this.hasProviderChanges(formData)) {
              return this.updateProviderObservable(providerId, formData, brokerResponse.data.id);
            } else {
              // Return a mock response with the broker data
              return of({ success: true, data: { ...this.originalProviderData(), broker_id: brokerResponse.data.id } as any, message: 'Broker updated' });
            }
          }),
          catchError((error) => {
            this.isCreatingBroker.set(false);
            this.isSubmitting.set(false);
            this.errorMessage.set(error.error?.message || 'Error al actualizar el corredor');
            console.error('Error updating broker:', error);
            return throwError(() => error);
          })
        ).subscribe({
          next: (response) => {
            this.isSubmitting.set(false);
            const message = this.hasProviderChanges(formData)
              ? 'Proveedor y corredor actualizados'
              : 'Corredor actualizado';
            const description = this.hasProviderChanges(formData)
              ? `El proveedor "${response.data.fantasy_name || formData.name}" y su corredor han sido actualizados correctamente.`
              : `El corredor ha sido actualizado correctamente.`;
            this.notificationService.success(message, description);
            this.dialogRef.close(response.data);
          },
          error: (error) => {
            this.isSubmitting.set(false);
            this.errorMessage.set(error.error?.message || 'Error al actualizar');
            console.error('Error updating:', error);
          }
        });
      }
      // If no existing broker, create new one
      else {
        this.isCreatingBroker.set(true);
        this.brokerService.createBroker(brokerDto).pipe(
          switchMap((brokerResponse) => {
            this.isCreatingBroker.set(false);
            return this.updateProviderObservable(providerId, formData, brokerResponse.data.id);
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
              'Proveedor actualizado y corredor creado',
              `El proveedor "${response.data.fantasy_name}" ha sido actualizado y el corredor creado correctamente.`
            );
            this.dialogRef.close(response.data);
          },
          error: (error) => {
            this.isSubmitting.set(false);
            this.errorMessage.set(error.error?.message || 'Error al actualizar el proveedor');
            console.error('Error updating provider:', error);
          }
        });
      }
    }
    // Scenario 3: No broker changes, check if provider changed
    else {
      if (this.hasProviderChanges(formData)) {
        this.updateProvider(providerId, formData, this.existingBrokerId());
      } else {
        // No changes at all, just close dialog
        this.isSubmitting.set(false);
        this.notificationService.success(
          'Sin cambios',
          'No se realizaron cambios en el proveedor.'
        );
        this.dialogRef.close();
      }
    }
  }

  updateProvider(providerId: number, formData: ProviderFormData, brokerId: number | null) {
    this.updateProviderObservable(providerId, formData, brokerId).subscribe({
      next: (response) => {
        this.isSubmitting.set(false);
        this.notificationService.success(
          'Proveedor actualizado',
          `El proveedor "${response.data.fantasy_name}" ha sido actualizado correctamente.`
        );
        this.dialogRef.close(response.data);
      },
      error: (error) => {
        this.isSubmitting.set(false);
        this.errorMessage.set(error.error?.message || 'Error al actualizar el proveedor');
        console.error('Error updating provider:', error);
      }
    });
  }

  updateProviderObservable(providerId: number, formData: ProviderFormData, brokerId: number | null) {
    const dto = mapProviderFormToDto(formData);
    dto.broker_id = brokerId;
    return this.providerService.updateProvider(providerId, dto);
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
