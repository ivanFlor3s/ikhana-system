/* eslint-disable @typescript-eslint/no-explicit-any */
import { CommonModule } from '@angular/common';
import { Component, Input, Output, EventEmitter, Directive, TemplateRef, ContentChildren, QueryList, signal, computed } from '@angular/core';


export type SortDirection = 'asc' | 'desc' | null;

export interface ColumnDef {
  id: string;
  title: string;
  sortable?: boolean;
  // accessor can be a property path like 'user.name' or a function (row) => value
  accessor?: string | ((row: any) => any);
  width?: string; // optional tailwind width classes (e.g. 'w-1/4')
}

@Directive({
  // eslint-disable-next-line @angular-eslint/directive-selector
  selector: 'ng-template[modernTableCell]',
  standalone: true
})
export class ModernTableCellDirective {
  @Input('modernTableCell') columnId!: string;
  // eslint-disable-next-line @angular-eslint/prefer-inject
  constructor(public template: TemplateRef<any>) { }
}

@Component({
  selector: 'app-table',
  imports: [CommonModule],
  templateUrl: './table.component.html',
  styleUrl: './table.component.css'
})
export class TableComponent {
  // Inputs
  @Input() columns: ColumnDef[] = [];
  @Input() data: any[] = [];
  @Input() pageSizeOptions: number[] = [5, 10, 20];
  @Input() initialPageSize = 10;

  // Optional outputs
  @Output() sortChanged = new EventEmitter<{ columnId: string | null; direction: SortDirection }>();
  @Output() pageChanged = new EventEmitter<{ page: number; pageSize: number }>();

  // Templates provided by consumer via ng-template modernTableCell="colId"
  @ContentChildren(ModernTableCellDirective) templates!: QueryList<ModernTableCellDirective>;

  // internal state (signals)
  page = signal(1);
  pageSize = signal(this.initialPageSize);
  sort = signal<{ columnId: string | null; direction: SortDirection }>({ columnId: null, direction: null });

  // computed values
  private dataSignal = computed(() => this.data ?? []);

  // sortedData: first sort, then paginate
  private sortedData = computed(() => {
    const raw = [...this.dataSignal()];
    const s = this.sort();
    if (!s.columnId || !s.direction) return raw;

    const col = this.columns.find(c => c.id === s.columnId);
    if (!col) return raw;

    const accessor = col.accessor;
    const getVal = (row: any) => {
      if (typeof accessor === 'function') return accessor(row);
      if (typeof accessor === 'string' && accessor.length) {
        return accessor.split('.').reduce((acc: any, part: string) => acc?.[part], row);
      }
      return row[col.id];
    };

    raw.sort((a, b) => {
      const va = getVal(a);
      const vb = getVal(b);

      // handle numbers and strings
      if (va == null && vb == null) return 0;
      if (va == null) return s.direction === 'asc' ? -1 : 1;
      if (vb == null) return s.direction === 'asc' ? 1 : -1;

      if (typeof va === 'number' && typeof vb === 'number') {
        return s.direction === 'asc' ? va - vb : vb - va;
      }

      const sa = String(va).toLowerCase();
      const sb = String(vb).toLowerCase();
      if (sa < sb) return s.direction === 'asc' ? -1 : 1;
      if (sa > sb) return s.direction === 'asc' ? 1 : -1;
      return 0;
    });

    return raw;
  });

  // pagination computed
  totalPages = computed(() => {
    const len = this.sortedData().length;
    const ps = this.pageSize();
    return Math.max(1, Math.ceil(len / ps));
  });

  displayRows = computed(() => {
    const start = (this.page() - 1) * this.pageSize();
    const end = start + this.pageSize();
    return this.sortedData().slice(start, end);
  });

  // lifecycle-ish helpers: ensure page in range when data / pageSize / sort changes
  constructor() {
    // reset page when data or pageSize or sort changes
    // With signals we can derive effects, but keep minimal: recompute page clamp when dependencies change
    // simple approach: whenever totalPages changes and page > totalPages => set page to totalPages
    // (we cannot import effect easily here but we can use computed and manual checks when interacting)
  }

  // template helpers
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  getValue(row: any, col: ColumnDef) {
    const accessor = col.accessor;
    if (typeof accessor === 'function') return accessor(row);
    if (typeof accessor === 'string' && accessor.length) {
      return accessor.split('.').reduce((acc: any, part: string) => acc?.[part], row) ?? '';
    }
    return row[col.id] ?? '';
  }

  // template template helpers
  hasTemplate(colId: string) {

    const hasIt = !!this.templates?.find(t => t.columnId === colId);
    console.log('hasTemplate', colId, hasIt);
    return hasIt;
  }

  getTemplate(colId: string) {
    const template = this.templates?.find(t => t.columnId === colId)?.template ?? null;
    console.log('getTemplate', colId, template);
    return template;
  }

  // trackBy for @for
  rowTrackBy = (index: number, item: any) => item?.id ?? index;

  // actions
  onHeaderClick(col: ColumnDef) {
    if (!col.sortable) return;

    const current = this.sort();
    let nextDir: SortDirection = 'asc';
    if (current.columnId === col.id) {
      if (current.direction === 'asc') nextDir = 'desc';
      else if (current.direction === 'desc') nextDir = null;
      else nextDir = 'asc';
    } else {
      nextDir = 'asc';
    }

    this.sort.set({ columnId: nextDir ? col.id : null, direction: nextDir });
    // reset page to 1 when sorting changes
    this.page.set(1);
    // emit
    this.sortChanged.emit({ columnId: this.sort().columnId, direction: this.sort().direction });
    this.pageChanged.emit({ page: this.page(), pageSize: this.pageSize() });
  }

  prevPage() {
    const p = Math.max(1, this.page() - 1);
    this.page.set(p);
    this.pageChanged.emit({ page: this.page(), pageSize: this.pageSize() });
  }

  nextPage() {
    const p = Math.min(this.totalPages(), this.page() + 1);
    this.page.set(p);
    this.pageChanged.emit({ page: this.page(), pageSize: this.pageSize() });
  }

  changePageSize(v: number | string) {
    const ps = Number(v);
    if (!Number.isFinite(ps) || ps <= 0) return;
    this.pageSize.set(ps);
    // clamp page
    const tp = this.totalPages();
    if (this.page() > tp) this.page.set(tp);
    this.pageChanged.emit({ page: this.page(), pageSize: this.pageSize() });
  }
}
