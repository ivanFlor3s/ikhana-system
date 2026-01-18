# Table Component

A flexible, feature-rich table component with sorting, pagination, custom templates, and value formatting.

## Basic Usage

```typescript
import { TableComponent, ColumnDef } from '@shared/components/table/table.component';

@Component({
  selector: 'app-my-list',
  imports: [TableComponent],
  template: `<app-table [columns]="columns" [data]="data" />`
})
export class MyListComponent {
  data = [
    { id: 1, name: 'John Doe', email: 'john@example.com' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com' }
  ];

  columns: ColumnDef[] = [
    { id: 'id', title: 'ID' },
    { id: 'name', title: 'Name', sortable: true },
    { id: 'email', title: 'Email' }
  ];
}
```

---

## Inputs

### `columns` (required)
**Type:** `ColumnDef[]`

Array of column definitions. Each column can have:

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `id` | `string` | ✅ | Unique column identifier |
| `title` | `string` | ✅ | Column header text |
| `sortable` | `boolean` | ❌ | Enable sorting for this column |
| `accessor` | `string \| function` | ❌ | How to access the value (see [Accessors](#accessors)) |
| `width` | `string` | ❌ | Tailwind width class (e.g., `'w-1/4'`, `'w-32'`) |
| `formatter` | `function` | ❌ | Transform value before display (see [Formatters](#formatters)) |
| `pipe` | `object` | ❌ | Angular pipe configuration (see [Pipes](#pipes)) |

### `data` (required)
**Type:** `any[]`

Array of data objects to display in the table.

### `pageSizeOptions`
**Type:** `number[]`  
**Default:** `[5, 10, 20]`

Available page size options for the user to select.

### `initialPageSize`
**Type:** `number`  
**Default:** `10`

Initial number of rows per page.

### `selectableRows`
**Type:** `boolean`  
**Default:** `false`

Enable row selection. Selected rows will be highlighted.

---

## Outputs

### `sortChanged`
**Type:** `EventEmitter<{ columnId: string | null; direction: 'asc' | 'desc' | null }>`

Emitted when sorting changes.

```typescript
<app-table 
  [columns]="columns" 
  [data]="data"
  (sortChanged)="onSort($event)" />
```

### `pageChanged`
**Type:** `EventEmitter<{ page: number; pageSize: number }>`

Emitted when page or page size changes.

### `rowClick`
**Type:** `EventEmitter<any>`

Emitted when any row is clicked.

### `rowSelect`
**Type:** `EventEmitter<any>`

Emitted when a row is selected (only when `selectableRows` is `true`).

---

## Accessors

Control how values are extracted from your data objects.

### Default Accessor
If no accessor is specified, uses the column `id` as the property name:

```typescript
{ id: 'name', title: 'Name' }
// Accesses: row.name
```

### String Path Accessor
Access nested properties using dot notation:

```typescript
{ 
  id: 'userName', 
  title: 'User Name',
  accessor: 'user.name' 
}
// Accesses: row.user.name
```

### Function Accessor
Use a custom function for complex logic:

```typescript
{ 
  id: 'fullName', 
  title: 'Full Name',
  accessor: (row) => `${row.firstName} ${row.lastName}`
}
```

---

## Formatters

Transform values before display using custom functions.

### Basic Formatter

```typescript
{
  id: 'price',
  title: 'Price',
  formatter: (value) => `$${value.toFixed(2)}`
}
```

### Formatter with Row Context

```typescript
{
  id: 'status',
  title: 'Status',
  formatter: (value, row) => {
    return row.isActive ? 'Active' : 'Inactive';
  }
}
```

### Complex Formatter Example

```typescript
{
  id: 'validations',
  title: 'Validations',
  formatter: (value) => {
    if (!value) return 'N/A';
    const passed = Object.values(value).filter(v => v === true).length;
    const total = Object.keys(value).length;
    return `${passed}/${total} passed`;
  }
}
```

---

## Pipes

Use Angular's built-in pipes for common formatting needs.

### Date Pipe

```typescript
{
  id: 'createdAt',
  title: 'Created',
  pipe: { name: 'date', args: ['dd/MM/yyyy'] }
}
```

### Number Pipe

```typescript
{
  id: 'amount',
  title: 'Amount',
  pipe: { name: 'number', args: ['1.2-2'] } // min 1 digit, 2 decimals
}
```

### Currency Pipe

```typescript
{
  id: 'price',
  title: 'Price',
  pipe: { name: 'currency', args: ['USD', 'symbol', '1.2-2'] }
}
```

### Percent Pipe

```typescript
{
  id: 'completion',
  title: 'Progress',
  pipe: { name: 'percent', args: ['1.0-2'] }
}
```

### Text Transformation Pipes

```typescript
// Uppercase
{ id: 'code', title: 'Code', pipe: { name: 'uppercase' } }

// Lowercase
{ id: 'email', title: 'Email', pipe: { name: 'lowercase' } }

// Title Case
{ id: 'name', title: 'Name', pipe: { name: 'titlecase' } }
```

### Combining Formatter + Pipe

```typescript
{
  id: 'weight',
  title: 'Weight (kg)',
  formatter: (value) => parseFloat(value), // Convert string to number
  pipe: { name: 'number', args: ['1.2-2'] } // Format with 2 decimals
}
```

---

## Custom Templates

For complete control over cell rendering, use custom templates.

### Basic Template

```html
<app-table [columns]="columns" [data]="data">
  <ng-template modernTableCell="actions" let-row>
    <button (click)="edit(row)">Edit</button>
    <button (click)="delete(row)">Delete</button>
  </ng-template>
</app-table>
```

### Template with Value

```html
<ng-template modernTableCell="status" let-row let-value="value">
  <span [class.text-green-600]="value === 'active'"
        [class.text-red-600]="value === 'inactive'">
    {{ value }}
  </span>
</ng-template>
```

### Template Context

Templates receive:
- `$implicit` - The entire row object
- `value` - The formatted value for this cell

---

## Complete Example

```typescript
import { Component } from '@angular/core';
import { TableComponent, ColumnDef } from '@shared/components/table/table.component';

@Component({
  selector: 'app-users-list',
  imports: [TableComponent],
  template: `
    <app-table 
      [columns]="columns" 
      [data]="users"
      [selectableRows]="true"
      [pageSizeOptions]="[10, 25, 50]"
      [initialPageSize]="25"
      (rowClick)="onRowClick($event)"
      (sortChanged)="onSort($event)">
      
      <!-- Custom template for actions column -->
      <ng-template modernTableCell="actions" let-user>
        <button (click)="editUser(user)">Edit</button>
        <button (click)="deleteUser(user)">Delete</button>
      </ng-template>
    </app-table>
  `
})
export class UsersListComponent {
  users = [
    {
      id: 1,
      firstName: 'John',
      lastName: 'Doe',
      email: 'john@example.com',
      createdAt: new Date('2024-01-15'),
      salary: 75000,
      status: 'active'
    },
    // ... more users
  ];

  columns: ColumnDef[] = [
    {
      id: 'id',
      title: 'ID',
      width: 'w-20',
      sortable: true
    },
    {
      id: 'fullName',
      title: 'Full Name',
      accessor: (row) => `${row.firstName} ${row.lastName}`,
      sortable: true,
      pipe: { name: 'titlecase' }
    },
    {
      id: 'email',
      title: 'Email',
      sortable: true,
      pipe: { name: 'lowercase' }
    },
    {
      id: 'createdAt',
      title: 'Joined',
      sortable: true,
      pipe: { name: 'date', args: ['dd/MM/yyyy'] }
    },
    {
      id: 'salary',
      title: 'Salary',
      sortable: true,
      pipe: { name: 'currency', args: ['USD', 'symbol', '1.0-0'] }
    },
    {
      id: 'status',
      title: 'Status',
      formatter: (value) => value.toUpperCase()
    },
    {
      id: 'actions',
      title: 'Actions',
      width: 'w-32'
    }
  ];

  onRowClick(user: any) {
    console.log('Row clicked:', user);
  }

  onSort(event: any) {
    console.log('Sort changed:', event);
  }

  editUser(user: any) {
    console.log('Edit user:', user);
  }

  deleteUser(user: any) {
    console.log('Delete user:', user);
  }
}
```

---

## Styling

The table uses Tailwind CSS classes. You can customize the appearance by:

1. **Column Widths**: Use the `width` property with Tailwind classes
2. **Custom Templates**: Apply your own classes in templates
3. **Component Styles**: Override styles in your component's CSS file

---

## Tips

✅ **Use formatters** for simple transformations  
✅ **Use pipes** for built-in Angular formatting  
✅ **Use templates** for complex UI with buttons, icons, etc.  
✅ **Combine approaches** - formatter to prepare data, pipe to format it  
✅ **Set sortable: true** only on columns that make sense to sort  
✅ **Use width classes** to prevent layout shifts
