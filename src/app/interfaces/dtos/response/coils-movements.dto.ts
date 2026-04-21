/**
 * {
    "data": [
        {
            "id": 1,
            "date": "2026-04-11T00:35:33+00:00",
            "coils_received": 100,
            "coils_returned": 0,
            "entry": {
                "id": 3,
                "entry_number": null,
                "remito": "123"
            }
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/providers/17/coil-movements?page=1",
        "last": "http://localhost:8000/api/providers/17/coil-movements?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://localhost:8000/api/providers/17/coil-movements?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://localhost:8000/api/providers/17/coil-movements",
        "per_page": 20,
        "to": 1,
        "total": 1
    },
    "success": true,
    "message": "Movimientos de bobinas obtenidos exitosamente"
}
 */
export interface CoilMovementDto {
    id: number;
    date: string;
    coils_received: number;
    coils_returned: number;
    entry: {
        id: number;
        entry_number: string | null;
        remito: string;
    }
}

export interface CoilMovementsResponseDto {
    data: CoilMovementDto[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
    success: boolean;
    message: string;
}