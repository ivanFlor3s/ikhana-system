import { IngresoCobre } from "@interfaces/mocks/cobre-ingreso-interface";

export const ingresosCobre: IngresoCobre[] = [
    {
        // Basic Information
        fecha: new Date('2024-08-30'),
        remito: '893',
        proveedor: 'Río Babel',
        materiaCobre: 'Cobre',
        pesoKg: 2493.00,
        lote: '1023',
        identificacionLote: 'EMB-1023',

        // Measurements
        diametroMedidoMm: 0.50,
        resistenciaOhmsKm: 87.70,
        estiramientoPercent: 21.5,

        // IRAM Validation Tests
        aspectoSuperficialLibreDefectos: true,
        limpieza: true,
        acondicionado: true,
        rectificacion: true,

        // Test Results
        resultado: 'CUMPLE',
        fechaEnsayo: new Date('2024-08-30'),
    },
    {
        // Basic Information
        fecha: new Date('2024-08-31'),
        remito: '1023',
        proveedor: 'Río Babel',
        materiaCobre: 'Cobre',
        pesoKg: 1509.00,
        lote: '1023',
        identificacionLote: 'EMB-1023-B',

        // Measurements
        diametroMedidoMm: 0.50,
        resistenciaOhmsKm: 88.30,
        estiramientoPercent: 22.0,

        // IRAM Validation Tests
        aspectoSuperficialLibreDefectos: true,
        limpieza: true,
        acondicionado: true,
        rectificacion: true,

        // Test Results
        resultado: 'CUMPLE',
        fechaEnsayo: new Date('2024-09-27'),
    },
    {
        // Basic Information
        fecha: new Date('2024-09-02'),
        remito: '1389',
        proveedor: 'Proveedor XYZ',
        materiaCobre: 'Cobre',
        pesoKg: 1520.00,
        lote: '1389',
        identificacionLote: 'EMB-1389',

        // Measurements
        diametroMedidoMm: 0.60,
        resistenciaOhmsKm: 90.30,
        estiramientoPercent: 20.8,

        // IRAM Validation Tests
        aspectoSuperficialLibreDefectos: true,
        limpieza: true,
        acondicionado: true,
        rectificacion: true,

        // Test Results
        resultado: 'CUMPLE',
        fechaEnsayo: new Date('2024-11-23'),
    },
    {
        // Basic Information
        fecha: new Date('2024-09-05'),
        remito: '17425',
        proveedor: 'Río Babel',
        materiaCobre: 'Cobre',
        pesoKg: 2028.00,
        lote: '4259',
        identificacionLote: 'EMB-4259',

        // Measurements
        diametroMedidoMm: 0.50,
        resistenciaOhmsKm: 88.70,
        estiramientoPercent: 21.2,

        // IRAM Validation Tests
        aspectoSuperficialLibreDefectos: true,
        limpieza: true,
        acondicionado: true,
        rectificacion: true,

        // Test Results
        resultado: 'CUMPLE',
        fechaEnsayo: new Date('2024-04-26'),
    },
    {
        // Basic Information
        fecha: new Date('2024-09-03'),
        remito: '1765',
        proveedor: 'Proveedor ABC',
        materiaCobre: 'Cobre',
        pesoKg: 3054.00,
        lote: '1649',
        identificacionLote: 'EMB-1649',

        // Measurements
        diametroMedidoMm: 0.50,
        resistenciaOhmsKm: 90.38,
        estiramientoPercent: 21.8,

        // IRAM Validation Tests
        aspectoSuperficialLibreDefectos: true,
        limpieza: true,
        acondicionado: true,
        rectificacion: true,

        // Test Results
        resultado: 'CUMPLE',
        fechaEnsayo: new Date('2024-06-03'),
    },
    {
        // Basic Information
        fecha: new Date('2024-09-06'),
        remito: '17425',
        proveedor: 'Río Babel',
        materiaCobre: 'Cobre',
        pesoKg: 1028.00,
        lote: '1775',
        identificacionLote: 'EMB-1775',

        // Measurements
        diametroMedidoMm: 0.50,
        resistenciaOhmsKm: 90.44,
        estiramientoPercent: 21.0,

        // IRAM Validation Tests
        aspectoSuperficialLibreDefectos: true,
        limpieza: true,
        acondicionado: true,
        rectificacion: true,

        // Test Results
        resultado: 'CUMPLE',
        fechaEnsayo: new Date('2024-11-26'),
    },
    {
        // Basic Information
        fecha: new Date('2024-09-07'),
        remito: '765',
        proveedor: 'Proveedor XYZ',
        materiaCobre: 'Cobre',
        pesoKg: 1008.00,
        lote: '765',
        identificacionLote: 'EMB-765',

        // Measurements
        diametroMedidoMm: 0.50,
        resistenciaOhmsKm: 90.44,
        estiramientoPercent: 21.5,

        // IRAM Validation Tests
        aspectoSuperficialLibreDefectos: true,
        limpieza: true,
        acondicionado: true,
        rectificacion: true,

        // Test Results
        resultado: 'CUMPLE',
        fechaEnsayo: new Date('2025-03-28'),
    }
];