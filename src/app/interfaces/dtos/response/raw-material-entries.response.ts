/**
 * {
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "raw_material_type_id": 1,
                "provider_id": 86,
                "raw_material_characteristic_id": 1,
                "entry_number": null,
                "remito": "300",
                "batch": "101",
                "entry_date": "2026-02-01T00:00:00.000000Z",
                "quantity_kg": 204,
                "coils_count": 40,
                "status": "approved",
                "observations": null,
                "created_at": "2026-02-01T22:59:55.000000Z",
                "updated_at": "2026-02-01T22:59:55.000000Z",
                "deleted_at": null,
                "type": {
                    "id": 1,
                    "name": "Cobre",
                    "created_at": "2026-01-19T00:39:17.000000Z",
                    "updated_at": "2026-01-19T00:39:17.000000Z",
                    "deleted_at": null
                },
                "provider": {
                    "id": 86,
                    "fantasy_name": "Ferretería Central",
                    "business_name": "Central de Insumos S.A.",
                    "cuit": "30-14785236-9",
                    "iibb": "1478523-6",
                    "tax_status_id": 2,
                    "agreement_id": 1,
                    "category_id": 9,
                    "broker_id": 1,
                    "phone_1": "11-4555-8888",
                    "phone_2": null,
                    "phone_3": null,
                    "phone_4": null,
                    "phone_5": null,
                    "email_1": "info@ferreteriacentral.com",
                    "email_2": null,
                    "email_3": null,
                    "email_4": null,
                    "email_5": null,
                    "address": "Av. Gaona 2500, CABA",
                    "website": "",
                    "contact_name": "Hugo Garcia",
                    "observations": "Ferretería industrial",
                    "business_hours_start": "08:00",
                    "business_hours_end": "19:00",
                    "created_at": "2026-01-26T23:35:21.000000Z",
                    "updated_at": "2026-01-26T23:35:21.000000Z",
                    "deleted_at": null
                },
                "characteristic": {
                    "id": 1,
                    "raw_material_type_id": 1,
                    "name": "Diámetro",
                    "decimal_value": "0.300",
                    "text_value": null,
                    "unit": "mm",
                    "created_at": "2026-01-19T00:39:17.000000Z",
                    "updated_at": "2026-01-19T00:39:17.000000Z",
                    "deleted_at": null,
                    "description": "0.300 mm"
                },
                "test": {
                    "id": 1,
                    "raw_material_entry_id": 1,
                    "test_date": "2026-02-01T00:00:00.000000Z",
                    "resistance_ohm_km": 200,
                    "elongation_pct": null,
                    "check_winding": true,
                    "check_cleanliness": true,
                    "check_packaging": true,
                    "check_identification": true,
                    "result": "OK",
                    "conducted_by": "El pato Lucas",
                    "approved_by": null,
                    "created_at": "2026-02-01T22:59:55.000000Z",
                    "updated_at": "2026-02-01T22:59:55.000000Z",
                    "deleted_at": null
                }
            }
        ],
        "first_page_url": "http://localhost:8000/api/raw-material-entries?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://localhost:8000/api/raw-material-entries?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://localhost:8000/api/raw-material-entries?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "http://localhost:8000/api/raw-material-entries",
        "per_page": 20,
        "prev_page_url": null,
        "to": 1,
        "total": 1
    },
    "message": "Entradas obtenidas exitosamente"
}
 */

import { ProviderInEntry } from "@models/provider.model";

export interface RawMaterialEntriesResponse {
    success: boolean;
    data: RawMaterialCobreEntry[];
    message: string;
}

export interface RawMaterialCobreEntry {
    id: number;
    raw_material_type_id: number;
    provider_id: number;
    raw_material_characteristic_id: number;
    entry_number: string | null;
    remito: string;
    batch: string;
    entry_date: string;
    quantity_kg: number;
    coils_count: number;
    status: string;
    observations: string | null;
    type: Type;
    provider: ProviderInEntry;
    characteristic: Characteristic;
    test: Test;
}

export interface Type {
    id: number;
    name: string;
}


export interface Characteristic {
    id: number;
    name: string;
    decimal_value: string;
    unit: string;
    description: string;
}

export interface Test {
    id: number;
    raw_material_entry_id: number;
    test_date: string;
    resistance_ohm_km: number;
    elongation_pct: number | null;
    check_winding: boolean;
    check_cleanliness: boolean;
    check_packaging: boolean;
    check_identification: boolean;
    result: string;
    conducted_by: string;
    approved_by: string | null;
}

export interface Link {
    url: string | null;
    label: string;
    active: boolean;
}
