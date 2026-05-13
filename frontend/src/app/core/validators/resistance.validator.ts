import { AbstractControl, AsyncValidatorFn, ValidationErrors } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { map, catchError, debounceTime, switchMap, take } from 'rxjs';
import { RawMaterialService } from '@services/raw-material.service';

/**
 * Creates an async validator that validates resistance against IRAM standards
 * @param rawMaterialService - Service to call the validation endpoint
 * @param characteristicId - The raw material characteristic ID to validate against
 * @returns AsyncValidatorFn that validates the resistance value
 */
export function resistanceValidator(
    rawMaterialService: RawMaterialService,
    characteristicId: number
): AsyncValidatorFn {
    return (control: AbstractControl): Observable<ValidationErrors | null> => {
        // If no value, don't validate (let required validator handle it)
        if (!control.value) {
            return of(null);
        }

        const resistanceValue = Number(control.value);

        // If not a valid number, don't validate (let other validators handle it)
        if (isNaN(resistanceValue)) {
            return of(null);
        }

        return of(control.value).pipe(
            debounceTime(500), // Wait 500ms after user stops typing
            switchMap(() =>
                rawMaterialService.validateResistance({
                    raw_material_characteristic_id: characteristicId,
                    resistance_ohm_km: resistanceValue
                })
            ),
            map(response => {
                // If validation passes, return null (no error)
                if (response.success && response.data.valid) {
                    return null;
                }

                // If validation fails, return error object
                return {
                    resistanceExceedsLimit: {
                        measured: response.data.measured,
                        maxAllowed: response.data.max_allowed,
                        message: response.message
                    }
                };
            }),
            catchError(() => {
                // On error, return validation error
                return of({
                    resistanceValidationError: {
                        message: 'Error al validar la resistencia. Por favor, intente nuevamente.'
                    }
                });
            }),
            take(1) // Complete after first emission
        );
    };
}
