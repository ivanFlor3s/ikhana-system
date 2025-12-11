export interface PaginatedResponse<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface ApiResponse<T> {
    success: boolean;
    data: T;
    message: string;
}

export interface ProviderFilters {
    page?: number;
    per_page?: number;
    search?: string;
    category_id?: number;
}
