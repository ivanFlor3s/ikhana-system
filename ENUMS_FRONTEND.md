# 🎯 Guía de Uso de Enums para el Frontend

## ✅ ¿Qué agregamos?

Agregamos **endpoints para obtener las opciones de los select boxes** de manera dinámica:

1. **Tax Status** (Posición frente al IVA) - 14 opciones
2. **Agreement** (Convenio) - 1 opción por ahora

## 📚 Nuevos Endpoints

### 1. Obtener opciones de Posición frente al IVA

```http
GET /api/tax-statuses
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "value": "1",
      "label": "IVA Responsable Inscripto"
    },
    {
      "value": "2",
      "label": "IVA Responsable no Inscripto"
    },
    {
      "value": "3",
      "label": "IVA no Responsable"
    },
    {
      "value": "4",
      "label": "IVA Sujeto Exento"
    },
    {
      "value": "5",
      "label": "Consumidor Final"
    },
    {
      "value": "6",
      "label": "Responsable Monotributo"
    },
    {
      "value": "7",
      "label": "Sujeto no Categorizado"
    },
    {
      "value": "8",
      "label": "Proveedor del Exterior"
    },
    {
      "value": "9",
      "label": "Cliente del Exterior"
    },
    {
      "value": "10",
      "label": "IVA Liberado – Ley Nº 19.640"
    },
    {
      "value": "11",
      "label": "IVA Responsable Inscripto – Agente de Percepción"
    },
    {
      "value": "12",
      "label": "Pequeño Contribuyente Eventual"
    },
    {
      "value": "13",
      "label": "Monotributista Social"
    },
    {
      "value": "14",
      "label": "Pequeño Contribuyente Eventual Social"
    }
  ],
  "message": "Tax status options retrieved successfully"
}
```

### 2. Obtener opciones de Convenio

```http
GET /api/agreements
```

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "value": "convenio_multilateral",
      "label": "Convenio Multilateral"
    }
  ],
  "message": "Agreement options retrieved successfully"
}
```

## 🔧 Cómo Implementarlo en el Frontend

### React / Next.js Example

```typescript
import { useState, useEffect } from 'react';

interface EnumOption {
  value: string;
  label: string;
}

interface EnumsState {
  taxStatuses: EnumOption[];
  agreements: EnumOption[];
}

function ProviderForm() {
  const [enums, setEnums] = useState<EnumsState>({
    taxStatuses: [],
    agreements: []
  });

  // Cargar opciones al montar el componente
  useEffect(() => {
    const loadEnums = async () => {
      try {
        // Cargar ambos enums en paralelo
        const [taxStatusRes, agreementsRes] = await Promise.all([
          fetch('http://localhost:8000/api/tax-statuses'),
          fetch('http://localhost:8000/api/agreements')
        ]);

        const taxStatuses = await taxStatusRes.json();
        const agreements = await agreementsRes.json();

        setEnums({
          taxStatuses: taxStatuses.data,
          agreements: agreements.data
        });
      } catch (error) {
        console.error('Error loading enums:', error);
      }
    };

    loadEnums();
  }, []);

  return (
    <form>
      {/* Select de Tax Status */}
      <div>
        <label>Posición frente al IVA</label>
        <select name="tax_status">
          <option value="">Seleccionar posición frente al IVA</option>
          {enums.taxStatuses.map(option => (
            <option key={option.value} value={option.value}>
              {option.label}
            </option>
          ))}
        </select>
      </div>

      {/* Select de Agreement */}
      <div>
        <label>Convenio</label>
        <select name="agreement">
          <option value="">Seleccionar convenio</option>
          {enums.agreements.map(option => (
            <option key={option.value} value={option.value}>
              {option.label}
            </option>
          ))}
        </select>
      </div>

      {/* Resto de campos... */}
    </form>
  );
}
```

### Vue.js Example

```vue
<template>
  <form>
    <!-- Select de Tax Status -->
    <div>
      <label>Posición frente al IVA</label>
      <select v-model="form.tax_status">
        <option value="">Seleccionar posición frente al IVA</option>
        <option 
          v-for="option in taxStatuses" 
          :key="option.value" 
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
    </div>

    <!-- Select de Agreement -->
    <div>
      <label>Convenio</label>
      <select v-model="form.agreement">
        <option value="">Seleccionar convenio</option>
        <option 
          v-for="option in agreements" 
          :key="option.value" 
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const taxStatuses = ref([]);
const agreements = ref([]);
const form = ref({
  tax_status: '',
  agreement: '',
  // otros campos...
});

onMounted(async () => {
  try {
    // Cargar opciones de Tax Status
    const taxStatusRes = await fetch('http://localhost:8000/api/tax-statuses');
    const taxStatusData = await taxStatusRes.json();
    taxStatuses.value = taxStatusData.data;

    // Cargar opciones de Agreement
    const agreementRes = await fetch('http://localhost:8000/api/agreements');
    const agreementData = await agreementRes.json();
    agreements.value = agreementData.data;
  } catch (error) {
    console.error('Error loading enums:', error);
  }
});
</script>
```

### Angular Example

```typescript
// provider-form.component.ts
import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';

interface EnumOption {
  value: string;
  label: string;
}

@Component({
  selector: 'app-provider-form',
  templateUrl: './provider-form.component.html'
})
export class ProviderFormComponent implements OnInit {
  taxStatuses: EnumOption[] = [];
  agreements: EnumOption[] = [];

  constructor(private http: HttpClient) {}

  ngOnInit() {
    this.loadEnums();
  }

  loadEnums() {
    // Cargar Tax Statuses
    this.http.get<any>('http://localhost:8000/api/tax-statuses')
      .subscribe(response => {
        this.taxStatuses = response.data;
      });

    // Cargar Agreements
    this.http.get<any>('http://localhost:8000/api/agreements')
      .subscribe(response => {
        this.agreements = response.data;
      });
  }
}
```

```html
<!-- provider-form.component.html -->
<form>
  <!-- Select de Tax Status -->
  <div>
    <label>Posición frente al IVA</label>
    <select [(ngModel)]="form.tax_status" name="tax_status">
      <option value="">Seleccionar posición frente al IVA</option>
      <option 
        *ngFor="let option of taxStatuses" 
        [value]="option.value"
      >
        {{ option.label }}
      </option>
    </select>
  </div>

  <!-- Select de Agreement -->
  <div>
    <label>Convenio</label>
    <select [(ngModel)]="form.agreement" name="agreement">
      <option value="">Seleccionar convenio</option>
      <option 
        *ngFor="let option of agreements" 
        [value]="option.value"
      >
        {{ option.label }}
      </option>
    </select>
  </div>
</form>
```

## 📝 Cuando envíes el formulario

Al crear o actualizar un proveedor, envía el **value** (no el label):

```javascript
// ✅ CORRECTO
const providerData = {
  business_name: "Mi Proveedor",
  cuit: "20-12345678-9",
  tax_status: "1",  // Envía el value
  agreement: "convenio_multilateral"  // Envía el value
};

// ❌ INCORRECTO
const providerData = {
  business_name: "Mi Proveedor",
  cuit: "20-12345678-9",
  tax_status: "IVA Responsable Inscripto",  // NO envíes el label
  agreement: "Convenio Multilateral"  // NO envíes el label
};
```

## 🔄 Flujo Completo

1. **Al cargar el formulario:**
   - Hacer GET a `/api/tax-statuses`
   - Hacer GET a `/api/agreements`
   - Guardar las opciones en el estado

2. **Al mostrar el formulario:**
   - Renderizar los selects con las opciones obtenidas

3. **Al editar un proveedor existente:**
   - El campo `tax_status` ya tiene el value (ej: "1")
   - El select automáticamente mostrará "IVA Responsable Inscripto"

4. **Al guardar:**
   - Enviar el value del select (ej: "1")
   - El backend valida que sea un valor válido
   - Si es inválido, devuelve error 422

## ⚡ Optimización

### Cache en LocalStorage

```javascript
// Guardar en localStorage para no hacer requests cada vez
const loadEnumsWithCache = async () => {
  const cacheKey = 'provider_enums';
  const cacheTime = 1000 * 60 * 60; // 1 hora

  // Intentar cargar del cache
  const cached = localStorage.getItem(cacheKey);
  if (cached) {
    const { data, timestamp } = JSON.parse(cached);
    if (Date.now() - timestamp < cacheTime) {
      return data;
    }
  }

  // Si no hay cache o expiró, hacer request
  const [taxStatusRes, agreementsRes] = await Promise.all([
    fetch('http://localhost:8000/api/tax-statuses'),
    fetch('http://localhost:8000/api/agreements')
  ]);

  const taxStatuses = await taxStatusRes.json();
  const agreements = await agreementsRes.json();

  const data = {
    taxStatuses: taxStatuses.data,
    agreements: agreements.data
  };

  // Guardar en cache
  localStorage.setItem(cacheKey, JSON.stringify({
    data,
    timestamp: Date.now()
  }));

  return data;
};
```

## 🎨 Con Libraries de UI

### React Select

```jsx
import Select from 'react-select';

<Select
  options={taxStatuses.map(opt => ({
    value: opt.value,
    label: opt.label
  }))}
  onChange={(selected) => setTaxStatus(selected.value)}
  placeholder="Seleccionar posición frente al IVA"
/>
```

### Material-UI

```jsx
import { FormControl, InputLabel, Select, MenuItem } from '@mui/material';

<FormControl fullWidth>
  <InputLabel>Posición frente al IVA</InputLabel>
  <Select
    value={taxStatus}
    onChange={(e) => setTaxStatus(e.target.value)}
  >
    {taxStatuses.map(option => (
      <MenuItem key={option.value} value={option.value}>
        {option.label}
      </MenuItem>
    ))}
  </Select>
</FormControl>
```

## 🚀 Ventajas de este Enfoque

1. ✅ **Mantenibilidad:** Si agregamos más opciones de IVA, el frontend se actualiza automáticamente
2. ✅ **Consistencia:** Las opciones siempre están sincronizadas con el backend
3. ✅ **Validación:** El backend valida que solo se envíen valores permitidos
4. ✅ **Escalabilidad:** Fácil agregar más enums (ej: provincias, ciudades, etc.)
5. ✅ **Internacionalización:** Puedes agregar idiomas fácilmente en el backend

## 📋 Testing

```javascript
describe('Provider Form', () => {
  it('should load tax status options', async () => {
    const response = await fetch('http://localhost:8000/api/tax-statuses');
    const data = await response.json();
    
    expect(data.success).toBe(true);
    expect(data.data).toHaveLength(14);
    expect(data.data[0]).toHaveProperty('value');
    expect(data.data[0]).toHaveProperty('label');
  });

  it('should validate invalid tax status', async () => {
    const response = await fetch('http://localhost:8000/api/providers', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        business_name: 'Test',
        cuit: '20-12345678-9',
        tax_status: '999' // Valor inválido
      })
    });

    const data = await response.json();
    expect(response.status).toBe(422);
    expect(data.errors.tax_status).toBeDefined();
  });
});
```

## 🔗 Links Útiles

- **Documentación:** http://localhost:8000/docs
- **Endpoint Tax Statuses:** http://localhost:8000/api/tax-statuses
- **Endpoint Agreements:** http://localhost:8000/api/agreements

