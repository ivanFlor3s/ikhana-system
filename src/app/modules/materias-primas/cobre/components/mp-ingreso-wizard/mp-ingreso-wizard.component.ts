import { Component, inject, signal } from '@angular/core';
import { FormBuilder, Validators, FormsModule, ReactiveFormsModule, AsyncValidatorFn } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatStepperModule } from '@angular/material/stepper';
import { MatButtonModule } from '@angular/material/button';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule, MatOption } from '@angular/material/core';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatRadioModule } from '@angular/material/radio';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { CommonModule, DatePipe } from '@angular/common';
import { IngresoCobre } from '@interfaces/mocks/cobre-ingreso-interface';
import { BadgeComponent } from '@shared/components/badge/badge.component';
import { ProviderService } from '@services/provider.service';
import { RawMaterialService, RawMaterialCharacteristic } from '@services/raw-material.service';
import { NameValue } from '@models/name-value.model';
import { map, take } from 'rxjs';
import { MatSelectModule } from '@angular/material/select';
import { resistanceValidator } from '@core/validators/resistance.validator';
import { CreateEntryRequest } from '@interfaces/dtos/create-entry.dto';
import { NotificationService } from '@services/notification.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-mp-ingreso-wizard',
  imports: [
    CommonModule,
    MatButtonModule,
    MatStepperModule,
    FormsModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatRadioModule,
    MatCardModule,
    MatIconModule,
    MatChipsModule,
    MatCheckboxModule,
    MatSelectModule,
    MatProgressSpinnerModule,
    BadgeComponent,
    MatOption,
  ],
  providers: [DatePipe],
  templateUrl: './mp-ingreso-wizard.component.html',
  styleUrl: './mp-ingreso-wizard.component.css'
})
export class MpIngresoWizardComponent {
  private _formBuilder = inject(FormBuilder);
  private _providerService = inject(ProviderService);
  private _rawMaterialService = inject(RawMaterialService);
  private _datePipe = inject(DatePipe);
  private _notificationService = inject(NotificationService);
  private _router = inject(Router);


  // Linear stepper - must complete each step
  isLinear = true;

  providers = signal<NameValue[]>([]);
  loadingProviders = signal(false);

  characteristics = signal<RawMaterialCharacteristic[]>([]);
  loadingCharacteristics = signal(false);

  minDate = new Date();

  // Copper type ID from API
  readonly COPPER_TYPE_ID = 1;

  // Step 1: Basic Information
  basicInfoFormGroup = this._formBuilder.group({
    fecha: [new Date(), Validators.required],
    remito: ['', Validators.required],
    proveedor: [null as number | null, Validators.required],
    cantidadBobinas: [null, [Validators.required, Validators.min(0.01)]],
    pesoKg: [null, [Validators.required, Validators.min(0.01)]],
    lote: ['', Validators.required],
    identificacionLote: ['', Validators.required],
  });

  // Step 2: Measurements
  // Note: diametroMedidoMm now stores the characteristic ID, not just the diameter value
  measurementsFormGroup = this._formBuilder.group({
    diametroMedidoMm: [null, [Validators.required]],
    resistenciaOhmsKm: [null, [Validators.required, Validators.min(0)]],
    estiramientoPercent: [21, [Validators.required, Validators.min(0), Validators.max(100)]],
    observacion: [''],
  });

  // Step 3: IRAM Validation Tests
  validationFormGroup = this._formBuilder.group({
    aspectoSuperficialLibreDefectos: [true, Validators.required],
    limpieza: [true, Validators.required],
    acondicionado: [true, Validators.required],
    rectificacion: [true, Validators.required],
    fechaEnsayo: [new Date(), Validators.required],
  });

  // Step 4: Return Bobinas (Summary step)
  returnBobinasFormGroup = this._formBuilder.group({
    returnToProvider: [false],
    cantidadBobinasDevolver: [null],
  });


  constructor() {
    this.loadProviders();
    this.loadCharacteristics();
    this.setupDiameterChangeListener();
    this.setupReturnBobinasListener();
  }

  /**
   * Setup listener for return to provider checkbox
   * When checked, make quantity field required
   */
  private setupReturnBobinasListener(): void {
    this.returnBobinasFormGroup.get('returnToProvider')?.valueChanges.subscribe(returnToProvider => {
      const cantidadControl = this.returnBobinasFormGroup.get('cantidadBobinasDevolver');

      if (returnToProvider) {
        cantidadControl?.setValidators([
          Validators.required,
          Validators.min(1),
        ]);
      } else {
        cantidadControl?.clearValidators();
        cantidadControl?.setValue(null);
      }

      cantidadControl?.updateValueAndValidity();
    });
  }

  get resistanceValidator(): AsyncValidatorFn {
    return resistanceValidator(this._rawMaterialService, this.measurementsFormGroup?.get('diametroMedidoMm')?.value || 0);
  }

  /**
   * Setup listener for diameter changes to update resistance validator
   * Also setup listeners for resistance and observation to handle conditional validation
   */
  private setupDiameterChangeListener(): void {
    this.measurementsFormGroup.get('diametroMedidoMm')?.valueChanges.subscribe(characteristicId => {
      if (characteristicId) {
        // Update the async validator with the selected characteristic ID
        this.measurementsFormGroup.get('resistenciaOhmsKm')?.setAsyncValidators(
          [this.resistanceValidator]
        );
        this.measurementsFormGroup.get('resistenciaOhmsKm')?.updateValueAndValidity();
      }
    });

    // Listen to resistance field changes to update observation field requirement
    this.measurementsFormGroup.get('resistenciaOhmsKm')?.valueChanges.subscribe((value: number | null) => {
      const resistanceControl = this.measurementsFormGroup.get('resistenciaOhmsKm');
      if (resistanceControl?.hasError('resistanceExceedsLimit')) {
        this.measurementsFormGroup.get('observacion')?.setValidators([Validators.required]);
        this.measurementsFormGroup.get('observacion')?.updateValueAndValidity();
      }
    });

    // Listen to observation field changes to revalidate the form
    this.measurementsFormGroup.get('observacion')?.valueChanges.subscribe((value: string | null) => {
      const resistanceControl = this.measurementsFormGroup.get('resistenciaOhmsKm');
      const needToRestoreValidation = value != null && value.trim().length == 0 && !resistanceControl?.hasAsyncValidator(this.resistanceValidator)
      if (needToRestoreValidation) {
        resistanceControl?.setAsyncValidators([this.resistanceValidator]);
      } else {
        resistanceControl?.clearAsyncValidators();
      }
      resistanceControl?.updateValueAndValidity();
    });
  }

  /**
   * Check if diameter measurement exists
   */
  isDiametroValid(): boolean {
    const medido = this.measurementsFormGroup.get('diametroMedidoMm')?.value;
    return medido !== null && medido !== undefined;
  }

  /**
   * Get the selected characteristic object
   */
  getSelectedCharacteristic(): RawMaterialCharacteristic | undefined {
    const characteristicId = this.measurementsFormGroup.get('diametroMedidoMm')?.value;
    if (!characteristicId) return undefined;
    return this.characteristics().find(c => c.id === Number(characteristicId));
  }

  /**
   * Calculate overall IRAM validation result
   */
  getResultado(): string {
    const aspecto = this.validationFormGroup.get('aspectoSuperficialLibreDefectos')?.value;
    const limpieza = this.validationFormGroup.get('limpieza')?.value;
    const acondicionado = this.validationFormGroup.get('acondicionado')?.value;
    const rectificacion = this.validationFormGroup.get('rectificacion')?.value;

    // All tests must pass (true) for CUMPLE
    if (aspecto && limpieza && acondicionado && rectificacion) {
      return 'CUMPLE';
    }
    return 'NO CUMPLE';
  }

  /**
   * Get summary data for final step
   */
  getSummaryData(): Partial<IngresoCobre> {
    return {
      ...this.basicInfoFormGroup.value,
      ...this.measurementsFormGroup.value,
      ...this.validationFormGroup.value,
      resultado: this.getResultado(),
      fechaEnsayo: new Date(),
    } as Partial<IngresoCobre>;
  }

  loadProviders(): void {
    this.loadingProviders.set(true);
    this._providerService.getProviders()
      .pipe(
        map((providers) => providers.data.data
          .map((provider) => ({ name: provider.fantasy_name, value: provider.id }))
          .sort((a, b) => a.name.localeCompare(b.name))
        ),
        take(1)
      )
      .subscribe({
        next: (providers) => {
          this.providers.set(providers);
          this.loadingProviders.set(false);
        },
        error: () => {
          this.loadingProviders.set(false);
        }
      });
  }

  /**
   * Load characteristics for copper material type
   */
  loadCharacteristics(): void {
    this.loadingCharacteristics.set(true);
    this._rawMaterialService.getCharacteristicsByType(this.COPPER_TYPE_ID)
      .pipe(take(1))
      .subscribe({
        next: (response) => {
          this.characteristics.set(response.data);
          this.loadingCharacteristics.set(false);
        },
        error: () => {
          this.loadingCharacteristics.set(false);
        }
      });
  }

  /**
   * Submit the copper ingreso
   */
  onSubmit(): void {
    if (this.basicInfoFormGroup.valid &&
      this.measurementsFormGroup.valid &&
      this.validationFormGroup.valid &&
      this.returnBobinasFormGroup.valid) {

      // TODO: Call service to save data
      // this.cobreService.createIngreso(ingresoData).subscribe(...)
      const dto: CreateEntryRequest = this.buildIngresoDto();
      this.publish(dto);
    }
  }

  private buildIngresoDto(): CreateEntryRequest {
    const dateEntry = this.basicInfoFormGroup.get('fecha')?.value!
    const dateEntryString = this._datePipe.transform(dateEntry, 'yyyy-MM-dd')!;
    return {
      raw_material_type_id: this.COPPER_TYPE_ID,
      provider_id: this.basicInfoFormGroup.get('proveedor')?.value! as number,
      raw_material_characteristic_id: this.measurementsFormGroup.get('diametroMedidoMm')?.value!,
      entry_date: dateEntryString,
      remito: this.basicInfoFormGroup.get('remito')?.value!,
      batch: this.basicInfoFormGroup.get('lote')?.value!,
      quantity_kg: this.basicInfoFormGroup.get('pesoKg')?.value!,
      coils_count: this.basicInfoFormGroup.get('cantidadBobinas')?.value!,
      observations: this.measurementsFormGroup.get('observacion')?.value!,
      test: {
        resistance_ohm_km: this.measurementsFormGroup.get('resistenciaOhmsKm')?.value!,
        check_winding: this.validationFormGroup.get('aspectoSuperficialLibreDefectos')?.value!,
        check_cleanliness: this.validationFormGroup.get('limpieza')?.value!,
        check_packaging: this.validationFormGroup.get('acondicionado')?.value!,
        check_identification: this.validationFormGroup.get('rectificacion')?.value!,
        conducted_by: 'El pato Lucas ',
      },
    };
  }

  private publish(dto: CreateEntryRequest): void {
    this._rawMaterialService.createEntry(dto)
      .pipe(take(1))
      .subscribe({
        next: (response) => {
          this._notificationService.success('Ingreso creado exitosamente');
          this._router.navigate(['/materias-primas/cobre']);
        },
        error: (error) => {
          this._notificationService.error('Error al crear ingreso');
        }
      });
  }
}
