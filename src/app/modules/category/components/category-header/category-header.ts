import { ChangeDetectionStrategy, Component, EventEmitter, Output } from '@angular/core';
import { MatButtonModule } from '@angular/material/button';
import { Button } from '../../../../shared/components/button/button';

@Component({
  selector: 'app-category-header',
  imports: [MatButtonModule, Button],
  templateUrl: './category-header.html',
  styleUrl: './category-header.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class CategoryHeader {

  @Output() createClicked = new EventEmitter<void>();

}
