import { AbstractControl, AsyncValidatorFn, ValidationErrors } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { map, catchError, debounceTime, switchMap, take } from 'rxjs/operators';
import { AfipService } from '../services/afip.service';

export function cuilAsyncValidator(afipService: AfipService): AsyncValidatorFn {
    return (control: AbstractControl): Observable<ValidationErrors | null> => {
        if (!control.value) {
            return of(null);
        }

        return of(control.value).pipe(
            debounceTime(500), // Wait 500ms after user stops typing
            switchMap(value =>
                afipService.validateTaxId(value).pipe(
                    map(response => {
                        // If exists is true, the CUIL is valid
                        return response.exists ? null : { cuilNotFound: true };
                    }),
                    catchError(() => {
                        // On error, return null to avoid blocking form submission
                        // You could also return an error here if you want to show an error message
                        return of(null);
                    })
                )
            ),
            take(1)
        );
    };
}
