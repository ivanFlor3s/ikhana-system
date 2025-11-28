# Autocomplete Component - Usage Guide

## Overview

The autocomplete component is a reusable Angular component that fetches `NameValue<T>` values from an API endpoint and supports both single and multiple selections.

## Features

- ✅ Generic `NameValue<T>` interface for type-safe API responses
- ✅ Debounced search (300ms) to reduce API calls
- ✅ Single and multiple selection modes
- ✅ Keyboard navigation (Arrow keys, Enter, Escape, Backspace)
- ✅ Loading and error states
- ✅ Chip display for multiple selections
- ✅ Click outside to close
- ✅ Modern, clean design with smooth animations

## Basic Usage

### Single Selection Mode

```typescript
import { Component } from '@angular/core';
import { AutocompleteComponent } from './shared/components/autocomplete/autocomplete.component';
import { NameValue } from './models/name-value.model';

@Component({
  selector: 'app-example',
  imports: [AutocompleteComponent],
  template: `
    <app-autocomplete
      [apiEndpoint]="'/api/categories'"
      [label]="'Category'"
      [placeholder]="'Search categories...'"
      (selectionChange)="onCategoryChange($event)">
    </app-autocomplete>
  `
})
export class ExampleComponent {
  onCategoryChange(category: NameValue | null) {
    console.log('Selected category:', category);
  }
}
```

### Multiple Selection Mode

```typescript
@Component({
  selector: 'app-example',
  imports: [AutocompleteComponent],
  template: `
    <app-autocomplete
      [apiEndpoint]="'/api/tags'"
      [label]="'Tags'"
      [placeholder]="'Search tags...'"
      [multiple]="true"
      (selectionChange)="onTagsChange($event)">
    </app-autocomplete>
  `
})
export class ExampleComponent {
  onTagsChange(tags: NameValue[] | null) {
    console.log('Selected tags:', tags);
  }
}
```

## API Requirements

Your API endpoint should:
1. Accept a `search` query parameter
2. Return data in this format:

```typescript
{
  success: boolean;
  data: NameValue<T>[];
}
```

Example API response:
```json
{
  "success": true,
  "data": [
    { "name": "Category 1", "value": 1 },
    { "name": "Category 2", "value": 2 },
    { "name": "Category 3", "value": 3 }
  ]
}
```

## Component Inputs

| Input | Type | Default | Description |
|-------|------|---------|-------------|
| `apiEndpoint` | `string` | `''` | API endpoint to fetch data from |
| `multiple` | `boolean` | `false` | Enable multiple selection mode |
| `placeholder` | `string` | `'Search...'` | Input placeholder text |
| `label` | `string` | `''` | Label for the autocomplete field |
| `disabled` | `boolean` | `false` | Disable the component |

## Component Outputs

| Output | Type | Description |
|--------|------|-------------|
| `selectionChange` | `EventEmitter<NameValue<T> \| NameValue<T>[] \| null>` | Emits when selection changes |

## Keyboard Navigation

- **Arrow Down**: Move highlight down / Open dropdown
- **Arrow Up**: Move highlight up
- **Enter**: Select highlighted option
- **Escape**: Close dropdown
- **Backspace**: Remove last selected item (multiple mode, when input is empty)

## Styling

The component uses a modern design that matches your existing components with:
- Clean borders and rounded corners
- Smooth hover and focus states
- Animated dropdown with slide-down effect
- Blue accent colors for selected items
- Responsive chip display for multiple selections
