import { ChangeDetectionStrategy, Component, inject } from '@angular/core';
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
import { Button } from '../../../../shared/components/button/button';
import { AfipService } from '../../../../services/afip.service';
import { cuilAsyncValidator } from '../../../../validators/cuil-validator';

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
    ivaPosition: ['', Validators.required],
    convenio: [''],
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
  });


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
      this.dialogRef.close(this.form.value);
    } else {
      this.form.markAllAsTouched();
    }
  }

}
