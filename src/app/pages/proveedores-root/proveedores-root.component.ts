import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ProveedoresHeader } from "@modules/proveedores/components/proveedores-header/proveedores-header";

@Component({
  selector: 'app-proveedores-root',
  imports: [RouterOutlet, ProveedoresHeader],
  templateUrl: './proveedores-root.component.html',
  styleUrl: './proveedores-root.component.css'
})
export class ProveedoresRootComponent {

}
