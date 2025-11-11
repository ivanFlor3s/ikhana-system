import { Component } from '@angular/core';
import { CategoryListComponent } from '../../modules/category/components/category-list/category-list.component';

@Component({
  selector: 'app-category-page',
  imports: [CategoryListComponent],
  templateUrl: './category-page.component.html',
  styleUrl: './category-page.component.css'
})
export class CategoryPageComponent {

}
