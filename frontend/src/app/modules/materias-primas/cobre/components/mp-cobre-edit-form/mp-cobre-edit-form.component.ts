import { Component, effect, inject, signal, input, OnInit } from '@angular/core';
import { FormBuilder, Validators, ReactiveFormsModule, AsyncValidatorFn } from '@angular/forms';
import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatButtonModule } from '@angular/material/button';
import { MatDatepickerModule } from '@angular/material/datepicker';
import { MatNativeDateModule, MatOption } from '@angular/material/core';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatRadioModule } from '@angular/material/radio';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { MatSelectModule } from '@angular/material/select';
import { CommonModule, DatePipe } from '@angular/common';
import { BadgeComponent } from '@shared/components/badge/badge.component';
import { ProviderService } from '@services/provider.service';
import { RawMaterialService, RawMaterialCharacteristic } from '@services/raw-material.service';
import { NameValue } from '@models/name-value.model';
import { map, take } from 'rxjs';
import { resistanceValidator } from '@core/validators/resistance.validator';
import { CreateEntryRequest } from '@interfaces/dtos/create-entry.dto';
import { NotificationService } from '@services/notification.service';
import { Router } from '@angular/router';
import { AppInitService } from '@services/app-init.service';
import { DiameterIramOhmMaxValue } from '@models/diameters-iram-max-values';
import { ProviderInventory } from '@interfaces/dtos/response/provider-inventory.response';
import { ProviderInventoryService } from '@services/provider-inventory.service';
import { RawMaterialCobreEntry } from '@interfaces/dtos/response/raw-material-entries.response';

@Component({
  selector: 'app-mp-cobre-edit-form',
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatDatepickerModule,
    MatNativeDateModule,
    MatRadioModule,
    MatCardModule,
    MatIconModule,
    MatCheckboxModule,
    MatSelectModule,
    MatProgressSpinnerModule,
    MatOption,
  ],
  providers: [DatePipe],
  templateUrl: './mp-cobre-edit-form.component.html',
  styleUrl: './mp-cobre-edit-form.component.css'
})
export class MpCobreEditFormComponent implements OnInit {
  private _formBuilder = inject(FormBuilder);
  private _providerService = inject(ProviderService);
  private _rawMaterialService = inject(RawMaterialService);
  private _datePipe = inject(DatePipe);
  private _notificationService = inject(NotificationService);
  private _router = inject(Router);
  private _providerInventoryService = inject(ProviderInventoryService);
  private appInitService = inject(AppInitService);

  readonly COPPER_TYPE_ID = 1;


  entryId = input.required<number>();

  loading = signal(true);
  loadedEntry = signal<RawMaterialCobreEntry | null>(null);

  providers = signal<NameValue[]>([]);
  loadingProviders = signal(false);

  characteristics = signal<RawMaterialCharacteristic[]>([]);
  loadingCharacteristics = signal(false);

  diametersWithResistance = signal<DiameterIramOhmMaxValue[]>([]);
  loadingDiametersWithResistance = signal(false);

  providerInventory = signal<ProviderInventory | null>(null);
  providerInventoryLoading = signal(false);

  batch = signal('');

  form = this._formBuilder.group({
    fecha: [new Date() as Date | null, Validators.required],
    remito: ['', Validators.required],
    proveedor: [{value: null as NameValue<number> | null, disabled: true}, Validators.required],
    cantidadBobinas: [null as number | null, [Validators.required, Validators.min(0.01)]],
    pesoKg: [null as number | null, [Validators.required, Validators.min(0.01)]],

    diametroMedidoMm: [null as number | null, [Validators.required]],
    resistenciaOhmsKm: [null as number | null, [Validators.required, Validators.min(0)]],
    estiramientoPercent: [null as number | null, [Validators.required, Validators.min(0), Validators.max(100)]],
    observacion: [''],

    aspectoSuperficialLibreDefectos: [true, Validators.required],
    limpieza: [true, Validators.required],
    acondicionado: [true, Validators.required],
    rectificacion: [true, Validators.required],
    fechaEnsayo: [new Date() as Date | null, Validators.required],

    returnToProvider: [false as boolean | null],
    cantidadBobinasDevolver: [null as number | null],
  });

  get isReturningCoils(): boolean {
    return this.form.get('returnToProvider')?.value === true;
  }

  getSelectedCharacteristic(): RawMaterialCharacteristic | undefined {
    const id = this.form.get('diametroMedidoMm')?.value;
    if (!id) return undefined;
    return this.characteristics().find(c => c.id === Number(id));
  }

  compareProvider(a: NameValue<number> | null, b: NameValue<number> | null): boolean {
    return a?.value === b?.value;
  }

  constructor() {
    this.loadCharacteristics();
    this.loadDiametersWithMaxResistance();

    this.setupDiameterListener();
    this.setupReturnListener();
    this.setupProviderListener();

    effect(() => {
      if (this.appInitService.categories().length > 0) {
        this.loadProviders();
      }
    })

    effect(() => {
      const entry = this.loadedEntry();
      const providerList = this.providers();
      if (entry && providerList.length > 0) {
        const provider = providerList.find(p => p.value === entry.provider.id);
        this.form.get('proveedor')?.setValue(provider ?? null);
      }
    });

    effect(() => {
      const entry = this.loadedEntry();
      const charList = this.characteristics();
      if (entry && charList.length > 0) {
        this.form.get('diametroMedidoMm')?.setValue(entry.characteristic.id);
      }
    });

    effect(() => {
      const entry = this.loadedEntry();
      if (entry && !this.loading()) {
        this.populateEntryFields(entry);
      }
    });
  }

  ngOnInit(): void {
    this.loadEntryForEdit(this.entryId());
  }

  private loadEntryForEdit(id: number): void {
    this.loading.set(true);
    this._rawMaterialService.getEntryById(id)
      .pipe(take(1))
      .subscribe({
        next: (response) => {
          this.loadedEntry.set(response.data);
          this.batch.set(response.data.batch ?? '');
          this.loading.set(false);
        },
        error: () => {
          this._notificationService.error('Error', 'No se pudo cargar la entrada');
          this.loading.set(false);
          this._router.navigate(['app', 'materias-primas', 'cobre']);
        }
      });
  }

  private setupDiameterListener(): void {
    this.form.get('diametroMedidoMm')?.valueChanges.subscribe((characteristicId) => {
      if (characteristicId) {
        this.form.get('resistenciaOhmsKm')?.setAsyncValidators([this.resistanceValidator]);
        this.form.get('resistenciaOhmsKm')?.updateValueAndValidity();
      }
    });

    this.form.get('resistenciaOhmsKm')?.valueChanges.subscribe(() => {
      const resistanceControl = this.form.get('resistenciaOhmsKm');
      if (resistanceControl?.hasError('resistanceExceedsLimit')) {
        this.form.get('observacion')?.setValidators([Validators.required]);
        this.form.get('observacion')?.updateValueAndValidity();
      }
    });

    this.form.get('observacion')?.valueChanges.subscribe((value: string | null) => {
      const resistanceControl = this.form.get('resistenciaOhmsKm');
      const needRestore = value != null && value.trim().length === 0
        && !resistanceControl?.hasAsyncValidator(this.resistanceValidator);
      if (needRestore) {
        resistanceControl?.setAsyncValidators([this.resistanceValidator]);
      } else {
        resistanceControl?.clearAsyncValidators();
      }
      resistanceControl?.updateValueAndValidity();
    });
  }

  private setupReturnListener(): void {
    this.form.get('returnToProvider')?.valueChanges.subscribe((returnToProvider) => {
      const cantidadControl = this.form.get('cantidadBobinasDevolver');
      if (returnToProvider) {
        cantidadControl?.setValidators([Validators.required, Validators.min(1)]);
        const max = this.providerInventory()?.coils_count ?? 0;
        if (max > 0) {
          cantidadControl?.addValidators(Validators.max(max));
        }
      } else {
        cantidadControl?.clearValidators();
        cantidadControl?.setValue(null);
      }
      cantidadControl?.updateValueAndValidity();
    });
  }

  private setupProviderListener(): void {
    this.form.get('proveedor')?.valueChanges.subscribe((provider) => {
      if (provider?.value) {
        this.loadProviderInventory(provider.value);
      }
    });
  }

  get resistanceValidator(): AsyncValidatorFn {
    return resistanceValidator(this._rawMaterialService, this.form.get('diametroMedidoMm')?.value || 0);
  }

  private loadProviders(): void {
    this.loadingProviders.set(true);
    this._providerService.getProviders({ category_id: this.appInitService.categories().find(c => c.name === 'Cobre')?.value })
      .pipe(
        map((providers) => providers.data.data
          .map((p) => ({ name: p.fantasy_name, value: p.id }))
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

  private loadCharacteristics(): void {
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

  private loadDiametersWithMaxResistance(): void {
    this._rawMaterialService.getCobreDiametersWithIramMaxResistance()
      .pipe(take(1))
      .subscribe({
        next: (diametersWithResistance) => {
          this.diametersWithResistance.set(diametersWithResistance);
          this.loadingDiametersWithResistance.set(false);
        },
        error: () => {
          this.loadingDiametersWithResistance.set(false);
        }
      });
  }

  private loadProviderInventory(providerId: number): void {
    this.providerInventoryLoading.set(true);
    this._providerInventoryService.getProviderInventory(providerId)
      .pipe(take(1))
      .subscribe({
        next: (response) => {
          this.providerInventory.set(response.data);
          this.providerInventoryLoading.set(false);
        },
        error: () => {
          this.providerInventoryLoading.set(false);
        }
      });
  }

  private populateEntryFields(entry: RawMaterialCobreEntry): void {
    this.form.patchValue({
      fecha: new Date(entry.entry_date + 'T00:00:00'),
      remito: entry.remito,
      cantidadBobinas: entry.coils_count,
      pesoKg: entry.quantity_kg,

      resistenciaOhmsKm: entry.test?.resistance_ohm_km,
      estiramientoPercent: entry.test?.elongation_pct ?? 21,
      observacion: entry.observations ?? '',

      aspectoSuperficialLibreDefectos: entry.test?.check_winding ?? true,
      limpieza: entry.test?.check_cleanliness ?? true,
      acondicionado: entry.test?.check_packaging ?? true,
      rectificacion: entry.test?.check_identification ?? true,
      fechaEnsayo: entry.test?.test_date ? new Date(entry.test.test_date + 'T00:00:00') : new Date(),

      returnToProvider: (entry.returned_coils_count ?? 0) > 0,
      cantidadBobinasDevolver: (entry.returned_coils_count ?? 0) > 0 ? entry.returned_coils_count! : null,
    });
  }

  private buildDto(): CreateEntryRequest {
    const dateEntry = this.form.get('fecha')?.value!;
    const dateEntryString = this._datePipe.transform(dateEntry, 'yyyy-MM-dd')!;
    return {
      raw_material_type_id: this.COPPER_TYPE_ID,
      provider_id: this.form.get('proveedor')?.value?.value! as number,
      raw_material_characteristic_id: this.form.get('diametroMedidoMm')?.value!,
      entry_date: dateEntryString,
      remito: this.form.get('remito')?.value!,
      quantity_kg: this.form.get('pesoKg')?.value!,
      coils_count: this.form.get('cantidadBobinas')?.value!,
      returned_coils_count: this.form.get('cantidadBobinasDevolver')?.value ?? 0,
      observations: this.form.get('observacion')?.value ?? '',
      test: {
        resistance_ohm_km: this.form.get('resistenciaOhmsKm')?.value!,
        check_winding: this.form.get('aspectoSuperficialLibreDefectos')?.value!,
        check_cleanliness: this.form.get('limpieza')?.value!,
        check_packaging: this.form.get('acondicionado')?.value!,
        check_identification: this.form.get('rectificacion')?.value!,
        conducted_by: 'El pato Lucas ',
      },
    };
  }

  onSubmit(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    const dto = this.buildDto();
    this._rawMaterialService.updateEntry(this.entryId(), dto)
      .pipe(take(1))
      .subscribe({
        next: () => {
          this._notificationService.success('Entrada actualizada exitosamente');
          this._router.navigate(['app', 'materias-primas', 'cobre']);
        },
        error: () => {
          this._notificationService.error('Error al actualizar la entrada');
        }
      });
  }

  onCancel(): void {
    this._router.navigate(['app', 'materias-primas', 'cobre']);
  }
}
