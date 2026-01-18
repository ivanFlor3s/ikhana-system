import { ChangeDetectionStrategy, Component, EventEmitter, Output } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { Button } from '@shared/components/button/button';
import { PageHeaderComponent } from '@shared/components/page-header/page-header.component';
import { Sprout, LucideAngularModule } from 'lucide-angular';

@Component({
    selector: 'app-materias-primas-header',
    imports: [MatButtonModule, Button, PageHeaderComponent, LucideAngularModule],
    templateUrl: './materias-primas-header.html',
    styleUrl: './materias-primas-header.css',
    changeDetection: ChangeDetectionStrategy.OnPush,
})
export class MateriasPrimasHeader {

    @Output() createClicked = new EventEmitter<void>();
    readonly SproutIcon = Sprout;
}
