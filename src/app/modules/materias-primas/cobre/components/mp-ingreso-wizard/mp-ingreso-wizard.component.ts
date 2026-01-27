import { Component, inject, signal } from '@angular/core';
import { FormBuilder, Validators, FormsModule, ReactiveFormsModule } from '@angular/forms';
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
import { CommonModule } from '@angular/common';
import { IngresoCobre } from '@interfaces/mocks/cobre-ingreso-interface';
import { BadgeComponent } from '@shared/components/badge/badge.component';
import { ProviderService } from '@services/provider.service';
import { RawMaterialService, RawMaterialCharacteristic } from '@services/raw-material.service';
import { NameValue } from '@models/name-value.model';
import { map, take } from 'rxjs';
import { MatSelectModule } from '@angular/material/select';
import { resistanceValidator } from '@core/validators/resistance.validator';

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
    MatSelectModule,
    MatProgressSpinnerModule,
    BadgeComponent,
    MatOption,
  ],
  templateUrl: './mp-ingreso-wizard.component.html',
  styleUrl: './mp-ingreso-wizard.component.css'
})
export class MpIngresoWizardComponent {
  private _formBuilder = inject(FormBuilder);
  private _providerService = inject(ProviderService);
  private _rawMaterialService = inject(RawMaterialService);

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
    proveedor: [null, Validators.required],
    cantidadBobinas: [null, [Validators.required, Validators.min(0.01)]],
    pesoKg: [null, [Validators.required, Validators.min(0.01)]],
    lote: ['', Validators.required],
    identificacionEmbalaje: ['', Validators.required],
  });

  // Step 2: Measurements
  // Note: diametroMedidoMm now stores the characteristic ID, not just the diameter value
  measurementsFormGroup = this._formBuilder.group({
    diametroMedidoMm: [null, [Validators.required]],
    resistenciaOhmsKm: [null, [Validators.required, Validators.min(0)]],
    estiramientoPercent: [21, [Validators.required, Validators.min(0), Validators.max(100)]],
  });

  // Step 3: IRAM Validation Tests
  validationFormGroup = this._formBuilder.group({
    aspectoSuperficialLibreDefectos: [true, Validators.required],
    limpieza: [true, Validators.required],
    acondicionado: [true, Validators.required],
    rectificacion: [true, Validators.required],
    realizadoPor: ['', Validators.required],
    controladoPor: ['', Validators.required],
  });

  constructor() {
    this.loadProviders();
    this.loadCharacteristics();
    this.setupDiameterChangeListener();
  }

  /**
   * Setup listener for diameter changes to update resistance validator
   */
  private setupDiameterChangeListener(): void {
    this.measurementsFormGroup.get('diametroMedidoMm')?.valueChanges.subscribe(characteristicId => {
      if (characteristicId) {
        // Update the async validator with the selected characteristic ID
        this.measurementsFormGroup.get('resistenciaOhmsKm')?.setAsyncValidators(
          [resistanceValidator(this._rawMaterialService, characteristicId)]
        );
        this.measurementsFormGroup.get('resistenciaOhmsKm')?.updateValueAndValidity();
      }
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
      this.validationFormGroup.valid) {

      const ingresoData: IngresoCobre = {
        // Basic Information
        fecha: this.basicInfoFormGroup.get('fecha')?.value!,
        remito: this.basicInfoFormGroup.get('remito')?.value!,
        proveedor: this.basicInfoFormGroup.get('proveedor')?.value!,
        materiaCobre: this.basicInfoFormGroup.get('materiaCobre')?.value!,
        pesoKg: this.basicInfoFormGroup.get('pesoKg')?.value!,
        lote: this.basicInfoFormGroup.get('lote')?.value!,
        identificacionEmbalaje: this.basicInfoFormGroup.get('identificacionEmbalaje')?.value!,

        // Measurements
        diametroMedidoMm: this.getSelectedCharacteristic()?.decimal_value!,
        resistenciaOhmsKm: this.measurementsFormGroup.get('resistenciaOhmsKm')?.value!,
        estiramientoPercent: this.measurementsFormGroup.get('estiramientoPercent')?.value!,

        // IRAM Validation
        aspectoSuperficialLibreDefectos: this.validationFormGroup.get('aspectoSuperficialLibreDefectos')?.value!,
        limpieza: this.validationFormGroup.get('limpieza')?.value!,
        acondicionado: this.validationFormGroup.get('acondicionado')?.value!,
        rectificacion: this.validationFormGroup.get('rectificacion')?.value!,

        // Results
        resultado: this.getResultado(),
        fechaEnsayo: new Date(),
        realizadoPor: this.validationFormGroup.get('realizadoPor')?.value!,
        controladoPor: this.validationFormGroup.get('controladoPor')?.value!,
      };

      console.log('Copper Ingreso Created:', ingresoData);
      // TODO: Call service to save data
      // this.cobreService.createIngreso(ingresoData).subscribe(...)
    }
  }
}
