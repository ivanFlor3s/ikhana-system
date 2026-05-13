/**
 * {
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "provider_id": 5,
                "provider_name": "Rio Batel Trafilación Cobre",
                "coils_count": 42,
                "last_movement_in": "2026-04-10T23:45:00.000000Z",
                "last_movement_out": "2026-04-08T14:00:00.000000Z"
            },
            {
                "provider_id": 8,
                "provider_name": "Cobre del Litoral S.A.",
                "coils_count": 15,
                "last_movement_in": "2026-04-05T10:30:00.000000Z",
                "last_movement_out": null
            }
        ],
        "first_page_url": "http://localhost:8000/api/providers/coil-summary?page=1",
        "from": 1,
        "last_page": 2,
        "last_page_url": "http://localhost:8000/api/providers/coil-summary?page=2",
        "next_page_url": "http://localhost:8000/api/providers/coil-summary?page=2",
        "path": "http://localhost:8000/api/providers/coil-summary",
        "per_page": 15,
        "prev_page_url": null,
        "to": 15,
        "total": 28
    },
    "message": "Resumen de bobinas por proveedor obtenido exitosamente"
}
 */

export interface CoilsSummaryItemDto {
    provider_id: number;
    provider_name: string;
    coils_count: number;
    last_movement_in: string;
    last_movement_out: string;
}

export interface CoilsSummaryResponseDto {
    current_page: number;
    data: CoilsSummaryItemDto[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: string;
    path: string;
    per_page: number;
    prev_page_url: string;
    to: number;
    total: number;
}