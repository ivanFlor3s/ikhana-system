export interface TaxStatus {
    id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}
