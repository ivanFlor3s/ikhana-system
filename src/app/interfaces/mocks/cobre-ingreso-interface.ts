export interface IngresoCobre {
    // Basic Information
    fecha: Date;
    remito: string;
    proveedor: string;
    materiaCobre: string;
    pesoKg: number;
    lote: string;
    identificacionEmbalaje: string;

    // Measurements
    diametroMedidoMm: number;
    resistenciaOhmsKm: number;
    estiramientoPercent: number;

    // IRAM Validation Tests (S/N)
    aspectoSuperficialLibreDefectos: boolean; // S = true, N = false
    limpieza: boolean;
    acondicionado: boolean;
    rectificacion: boolean;

    // Test Results
    resultado: string; // "CUMPLE" or "NO CUMPLE"
    fechaEnsayo: Date;
    realizadoPor: string;
    controladoPor: string;
}