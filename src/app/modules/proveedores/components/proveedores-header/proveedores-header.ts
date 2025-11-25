import { ChangeDetectionStrategy, Component, EventEmitter, Output } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { Button } from '../../../../shared/components/button/button';
import { PageHeaderComponent } from '../../../../shared/components/page-header/page-header.component';

@Component({
    selector: 'app-proveedores-header',
    imports: [MatButtonModule, Button, PageHeaderComponent],
    templateUrl: './proveedores-header.html',
    styleUrl: './proveedores-header.css',
    changeDetection: ChangeDetectionStrategy.OnPush,
})
export class ProveedoresHeader {

    @Output() createClicked = new EventEmitter<void>();

}
