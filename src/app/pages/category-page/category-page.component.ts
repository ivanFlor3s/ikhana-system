import { Component } from '@angular/core';
import { CategoryListComponent } from '../../modules/category/components/category-list/category-list.component';
import { CategoryHeader } from '../../modules/category/components/category-header/category-header';
import { Provider } from '../../models/provider';

@Component({
  selector: 'app-category-page',
  imports: [CategoryListComponent, CategoryHeader],
  templateUrl: './category-page.component.html',
  styleUrl: './category-page.component.css'
})
export class CategoryPageComponent {

  providers: Provider[] = [
    {
      id: 1,
      name: 'Tech Solutions SA',
      cuit: '30-71583210-9',
      iib: '123-456789-0',
      address: 'Av. Corrientes 1234, CABA',
      socialReason: 'Tech Solutions Sociedad Anónima',
      ivaPosition: 'Responsable Inscripto',
      convenio: 'No',
      website: 'https://www.techsolutions.com',

      phone: '011-4555-1212',
      otherPhones: ['011-4555-3434', '011-4555-5656'],

      email: 'contacto@techsolutions.com',
      otherEmails: ['soporte@techsolutions.com', 'ventas@techsolutions.com'],

      observations: 'Proveedor confiable, responde rápido.',

      since: { hour: 9, minute: 0 },
      to: { hour: 18, minute: 0 }
    },
    {
      id: 2,
      name: 'Distribuidora El Cóndor',
      cuit: '20-30581294-3',
      iib: '456-987321-4',
      address: 'Calle Falsa 742, La Plata',
      socialReason: 'Distribuidora El Cóndor SRL',
      ivaPosition: 'Monotributo',
      convenio: 'Sí',
      website: 'https://www.elcondor.com',

      phone: '221-430-5678',
      otherPhones: ['221-430-9999'],

      email: 'info@elcondor.com',
      otherEmails: [],

      observations: 'Hace entregas solo de lunes a viernes.',

      since: { hour: 8, minute: 30 },
      to: { hour: 17, minute: 0 }
    },
    {
      id: 3,
      name: 'Soluciones Médicas SRL',
      cuit: '30-98541235-6',
      iib: '789-123654-8',
      address: 'Av. Santa Fe 5400, CABA',
      socialReason: 'Soluciones Médicas SRL',
      ivaPosition: 'Responsable Inscripto',
      convenio: 'No',
      website: 'https://www.solucionesmedicas.com',

      phone: '011-4700-8000',
      otherPhones: [],

      email: 'administracion@solucionesmedicas.com',
      otherEmails: ['pagos@solucionesmedicas.com'],

      observations: 'Solicitan orden de compra previa.',

      since: { hour: 10, minute: 0 },
      to: { hour: 16, minute: 30 }
    }
  ]

}
