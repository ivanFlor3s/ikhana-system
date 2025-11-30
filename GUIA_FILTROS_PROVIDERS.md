# 🔍 Guía de Filtros y Paginación - Providers

## Endpoint

```
GET /api/providers
```

## Query Parameters Disponibles

| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `search` | string | Busca en `fantasy_name` o `business_name` | `?search=Construcción` |
| `category_id` | integer | Filtra por categoría/rubro | `?category_id=1` |
| `page` | integer | Número de página (default: 1) | `?page=2` |
| `per_page` | integer | Items por página (default: 15, max: 100) | `?per_page=20` |

---

## 📊 Estructura de Respuesta

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "fantasy_name": "ProvConst",
        "business_name": "Proveedor de Construcción SA",
        "cuit": "33-22222222-9",
        "category_id": 1,
        "category": {
          "id": 1,
          "name": "Construcción",
          "description": "..."
        },
        "tax_status": {
          "id": 1,
          "name": "Responsable Inscripto"
        },
        "agreement": null,
        "phone_1": "+54 11 4444-5555",
        "email_1": "info@provconst.com",
        "created_at": "2024-11-30T10:00:00.000000Z"
      }
    ],
    "first_page_url": "http://localhost:8000/api/providers?page=1",
    "from": 1,
    "last_page": 3,
    "last_page_url": "http://localhost:8000/api/providers?page=3",
    "links": [...],
    "next_page_url": "http://localhost:8000/api/providers?page=2",
    "path": "http://localhost:8000/api/providers",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 45
  },
  "message": "Proveedores obtenidos exitosamente"
}
```

**Campos importantes para paginación:**
- `current_page`: Página actual
- `total`: Total de registros encontrados
- `per_page`: Items por página
- `last_page`: Última página disponible
- `next_page_url`: URL de la siguiente página (null si no hay)
- `prev_page_url`: URL de la página anterior (null si no hay)
- `data`: Array con los proveedores de la página actual

---

## 🧪 Ejemplos de Uso

### 1. Listar todos (sin filtros)

```bash
GET /api/providers
```

```javascript
fetch('http://localhost:8000/api/providers')
  .then(r => r.json())
  .then(result => {
    console.log('Total:', result.data.total);
    console.log('Proveedores:', result.data.data);
  });
```

---

### 2. Búsqueda por texto

Busca en `fantasy_name` **O** `business_name`:

```bash
GET /api/providers?search=Construcción
```

**Encuentra:**
- "Materiales **Construcción** Norte SA" ✓
- "**Construcción** y Obra SA" ✓
- "MatConst Norte" (porque business_name contiene "Construcción") ✓

```javascript
const searchTerm = 'Construcción';
fetch(`http://localhost:8000/api/providers?search=${encodeURIComponent(searchTerm)}`)
  .then(r => r.json())
  .then(result => {
    console.log(`Encontrados: ${result.data.total} proveedores`);
  });
```

---

### 3. Filtro por categoría

```bash
GET /api/providers?category_id=1
```

Muestra solo proveedores de categoría "Construcción" (id=1)

```javascript
const categoryId = 1;
fetch(`http://localhost:8000/api/providers?category_id=${categoryId}`)
  .then(r => r.json())
  .then(result => {
    console.log('Proveedores de Construcción:', result.data.data);
  });
```

---

### 4. Búsqueda + Filtro de categoría

```bash
GET /api/providers?search=Tech&category_id=2
```

Busca "Tech" en nombres **Y** filtra por categoría Tecnología (id=2)

```javascript
const searchTerm = 'Tech';
const categoryId = 2;
fetch(`http://localhost:8000/api/providers?search=${searchTerm}&category_id=${categoryId}`)
  .then(r => r.json())
  .then(result => {
    console.log('Proveedores de Tecnología que contienen "Tech":', result.data.data);
  });
```

---

### 5. Paginación

```bash
GET /api/providers?page=2&per_page=20
```

```javascript
const page = 2;
const perPage = 20;
fetch(`http://localhost:8000/api/providers?page=${page}&per_page=${perPage}`)
  .then(r => r.json())
  .then(result => {
    console.log(`Página ${result.data.current_page} de ${result.data.last_page}`);
    console.log(`Mostrando ${result.data.from}-${result.data.to} de ${result.data.total}`);
  });
```

---

### 6. Todo combinado

```bash
GET /api/providers?search=Construcción&category_id=1&page=1&per_page=10
```

```javascript
function getProviders(filters) {
  const params = new URLSearchParams();
  
  if (filters.search) params.append('search', filters.search);
  if (filters.categoryId) params.append('category_id', filters.categoryId);
  if (filters.page) params.append('page', filters.page);
  if (filters.perPage) params.append('per_page', filters.perPage);
  
  return fetch(`http://localhost:8000/api/providers?${params}`)
    .then(r => r.json());
}

// Uso:
getProviders({
  search: 'Construcción',
  categoryId: 1,
  page: 1,
  perPage: 10
}).then(result => {
  console.log(result.data);
});
```

---

## 🎯 Ejemplo Completo - Componente React

```jsx
import { useState, useEffect } from 'react';

function ProvidersList() {
  const [providers, setProviders] = useState([]);
  const [pagination, setPagination] = useState({});
  const [filters, setFilters] = useState({
    search: '',
    categoryId: '',
    page: 1,
    perPage: 15
  });

  useEffect(() => {
    fetchProviders();
  }, [filters]);

  const fetchProviders = async () => {
    const params = new URLSearchParams();
    if (filters.search) params.append('search', filters.search);
    if (filters.categoryId) params.append('category_id', filters.categoryId);
    params.append('page', filters.page);
    params.append('per_page', filters.perPage);

    const response = await fetch(
      `http://localhost:8000/api/providers?${params}`
    );
    const result = await response.json();

    setProviders(result.data.data);
    setPagination({
      currentPage: result.data.current_page,
      lastPage: result.data.last_page,
      total: result.data.total,
      perPage: result.data.per_page
    });
  };

  const handleSearchChange = (e) => {
    setFilters({ ...filters, search: e.target.value, page: 1 });
  };

  const handleCategoryChange = (e) => {
    setFilters({ ...filters, categoryId: e.target.value, page: 1 });
  };

  const handlePageChange = (newPage) => {
    setFilters({ ...filters, page: newPage });
  };

  return (
    <div>
      {/* Buscador */}
      <input
        type="text"
        placeholder="Buscar por nombre..."
        value={filters.search}
        onChange={handleSearchChange}
      />

      {/* Filtro de categoría */}
      <select
        value={filters.categoryId}
        onChange={handleCategoryChange}
      >
        <option value="">Todas las categorías</option>
        <option value="1">Construcción</option>
        <option value="2">Tecnología</option>
        {/* ... más categorías */}
      </select>

      {/* Tabla de proveedores */}
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre Fantasía</th>
            <th>Razón Social</th>
            <th>Categoría</th>
          </tr>
        </thead>
        <tbody>
          {providers.map(provider => (
            <tr key={provider.id}>
              <td>{provider.id}</td>
              <td>{provider.fantasy_name}</td>
              <td>{provider.business_name}</td>
              <td>{provider.category?.name || 'Sin categoría'}</td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Paginación */}
      <div>
        <button
          disabled={pagination.currentPage === 1}
          onClick={() => handlePageChange(pagination.currentPage - 1)}
        >
          Anterior
        </button>
        
        <span>
          Página {pagination.currentPage} de {pagination.lastPage}
          ({pagination.total} registros)
        </span>
        
        <button
          disabled={pagination.currentPage === pagination.lastPage}
          onClick={() => handlePageChange(pagination.currentPage + 1)}
        >
          Siguiente
        </button>
      </div>
    </div>
  );
}

export default ProvidersList;
```

---

## 📝 Notas Importantes

1. **El buscador es case-insensitive**: Busca "construcción", "Construcción" o "CONSTRUCCIÓN" da el mismo resultado
2. **La búsqueda es tipo LIKE**: Busca coincidencias parciales (ej: "Tech" encuentra "TechProv", "Tecnología", etc.)
3. **Los filtros son opcionales**: Si no envías ningún parámetro, devuelve todos los proveedores paginados
4. **Límite de per_page**: Máximo 100 items por página
5. **Resetear página**: Al cambiar filtros, es buena práctica volver a `page=1`

---

## 🔗 Documentación Completa

Para ver ejemplos interactivos y probar el endpoint directamente:

👉 **http://localhost:8000/docs**

