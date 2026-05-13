export interface Rubro {
    id: number;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    providers_count?: number; // Optional, returned in detail view
}
