export interface CreateEntryRequest {
    raw_material_type_id: number;
    provider_id: number;
    raw_material_characteristic_id: number;
    entry_date: string;
    remito: string;
    quantity_kg: number;
    coils_count: number;
    returned_coils_count: number;
    observations: string;
    test: {
        resistance_ohm_km: number;
        check_winding: boolean;
        check_cleanliness: boolean;
        check_packaging: boolean;
        check_identification: boolean;
        conducted_by: string;
    };
}