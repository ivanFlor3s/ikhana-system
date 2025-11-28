/* eslint-disable @typescript-eslint/no-explicit-any */
import { Component, Input, Output, EventEmitter, signal, computed, effect, inject, ElementRef, ViewChild } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { debounceTime, Subject, switchMap, catchError, of } from 'rxjs';
import { NameValue } from '../../../models/name-value.model';

@Component({
    selector: 'app-autocomplete',
    imports: [CommonModule, FormsModule],
    templateUrl: './autocomplete.component.html',
    styleUrl: './autocomplete.component.css'
})
export class AutocompleteComponent<T = number> {
    // Inputs
    @Input() apiEndpoint = '';
    @Input() multiple = false;
    @Input() placeholder = 'Search...';
    @Input() label = '';
    @Input() disabled = false;

    // Outputs
    @Output() selectionChange = new EventEmitter<NameValue<T> | NameValue<T>[] | null>();

    // ViewChild
    @ViewChild('searchInput') searchInput?: ElementRef<HTMLInputElement>;

    // Services
    private http = inject(HttpClient);

    // Signals
    searchTerm = signal('');
    isOpen = signal(false);
    isLoading = signal(false);
    error = signal<string | null>(null);
    options = signal<NameValue<T>[]>([]);
    selectedItems = signal<NameValue<T>[]>([]);
    highlightedIndex = signal(-1);

    // Computed
    filteredOptions = computed(() => {
        const opts = this.options();
        const selected = this.selectedItems();

        // Filter out already selected items in multiple mode
        if (this.multiple) {
            return opts.filter(opt => !selected.some(s => s.value === opt.value));
        }

        return opts;
    });

    displayValue = computed(() => {
        const selected = this.selectedItems();
        if (selected.length === 0) return '';
        if (this.multiple) return '';
        return selected[0]?.name || '';
    });

    // Search subject for debouncing
    private searchSubject = new Subject<string>();

    constructor() {
        // Setup debounced search
        this.searchSubject
            .pipe(
                debounceTime(300),
                switchMap(term => {
                    if (!term || term.length < 1) {
                        return of({ success: true, data: [] as NameValue<T>[] });
                    }

                    this.isLoading.set(true);
                    this.error.set(null);

                    const url = `${this.apiEndpoint}?search=${encodeURIComponent(term)}`;
                    return this.http.get<{ success: boolean; data: NameValue<T>[] }>(url).pipe(
                        catchError(err => {
                            console.error('Autocomplete error:', err);
                            this.error.set('Failed to load options');
                            return of({ success: false, data: [] as NameValue<T>[] });
                        })
                    );
                })
            )
            .subscribe(response => {
                this.isLoading.set(false);
                if (response.success) {
                    this.options.set(response.data);
                }
            });

        // Watch for search term changes
        effect(() => {
            const term = this.searchTerm();
            this.searchSubject.next(term);
        });
    }

    onInputFocus() {
        if (!this.disabled) {
            this.isOpen.set(true);
            // Trigger initial search if empty
            if (this.searchTerm() === '' && this.options().length === 0) {
                this.searchSubject.next('');
            }
        }
    }

    onInputChange(value: string) {
        this.searchTerm.set(value);
        this.isOpen.set(true);
        this.highlightedIndex.set(-1);
    }

    selectOption(option: NameValue<T>) {
        if (this.multiple) {
            const current = [...this.selectedItems()];
            current.push(option);
            this.selectedItems.set(current);
            this.selectionChange.emit(current);
            this.searchTerm.set('');
            this.highlightedIndex.set(-1);
            // Keep dropdown open in multiple mode
            this.searchInput?.nativeElement.focus();
        } else {
            this.selectedItems.set([option]);
            this.selectionChange.emit(option);
            this.searchTerm.set('');
            this.isOpen.set(false);
        }
    }

    removeItem(item: NameValue<T>, event?: Event) {
        event?.stopPropagation();
        const current = this.selectedItems().filter(i => i.value !== item.value);
        this.selectedItems.set(current);
        this.selectionChange.emit(this.multiple ? current : null);
    }

    clearSelection(event?: Event) {
        event?.stopPropagation();
        this.selectedItems.set([]);
        this.selectionChange.emit(null);
        this.searchTerm.set('');
        this.searchInput?.nativeElement.focus();
    }

    onKeyDown(event: KeyboardEvent) {
        const filtered = this.filteredOptions();

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                if (!this.isOpen()) {
                    this.isOpen.set(true);
                } else {
                    const newIndex = Math.min(this.highlightedIndex() + 1, filtered.length - 1);
                    this.highlightedIndex.set(newIndex);
                }
                break;

            case 'ArrowUp':
                event.preventDefault();
                const newIndex = Math.max(this.highlightedIndex() - 1, -1);
                this.highlightedIndex.set(newIndex);
                break;

            case 'Enter':
                event.preventDefault();
                const index = this.highlightedIndex();
                if (index >= 0 && index < filtered.length) {
                    this.selectOption(filtered[index]);
                }
                break;

            case 'Escape':
                event.preventDefault();
                this.isOpen.set(false);
                this.highlightedIndex.set(-1);
                break;

            case 'Backspace':
                if (this.multiple && this.searchTerm() === '' && this.selectedItems().length > 0) {
                    const items = [...this.selectedItems()];
                    items.pop();
                    this.selectedItems.set(items);
                    this.selectionChange.emit(items);
                }
                break;
        }
    }

    onClickOutside(event: MouseEvent) {
        const target = event.target as HTMLElement;
        if (!target.closest('.autocomplete-container')) {
            this.isOpen.set(false);
            this.highlightedIndex.set(-1);
        }
    }

    trackByValue = (index: number, item: NameValue<T>) => item.value;
}
