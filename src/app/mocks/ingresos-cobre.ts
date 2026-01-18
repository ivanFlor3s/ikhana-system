import { IngresoCobre } from "@interfaces/mocks/cobre-ingreso-interface";

export const ingresosCobre: IngresoCobre[] = [
    {
        fecha: new Date(),
        remito: '123456',
        provider: 'Proveedor 1',
        weight: '4000',
        diameter: 10,
        lote: 'LOTE-001',
        validations: {
            embalaje: true,
            superficialAspect: true,
            recocido: true
        },
        resultado: 'Ok',
        dateOfTest: new Date()
    },
    {
        fecha: new Date(),
        remito: '123456',
        provider: 'Proveedor 2',
        weight: '421',
        diameter: 10,
        lote: 'LOTE-002',
        validations: {
            embalaje: true,
            superficialAspect: true,
            recocido: true
        },
        resultado: 'Ok',
        dateOfTest: new Date()
    },
    {
        fecha: new Date(),
        remito: '123456',
        provider: 'Proveedor 3',
        weight: '311',
        diameter: 10,
        lote: 'LOTE-003',
        validations: {
            embalaje: true,
            superficialAspect: true,
            recocido: true
        },
        resultado: 'Ok',
        dateOfTest: new Date()
    },
    {
        fecha: new Date(),
        remito: '123456',
        provider: 'Proveedor 4',
        weight: '1200',
        diameter: 10,
        lote: 'LOTE-004',
        validations: {
            embalaje: true,
            superficialAspect: true,
            recocido: true
        },
        resultado: 'Ok',
        dateOfTest: new Date()
    },
    {
        fecha: new Date(),
        remito: '123456',
        provider: 'Proveedor 5',
        weight: '1000',
        diameter: 10,
        lote: 'LOTE-005',
        validations: {
            embalaje: true,
            superficialAspect: true,
            recocido: true
        },
        resultado: 'Ok',
        dateOfTest: new Date()
    },
    {
        fecha: new Date(),
        remito: '123456',
        provider: 'Proveedor 6',
        weight: '1000',
        diameter: 10,
        lote: 'LOTE-006',
        validations: {
            embalaje: true,
            superficialAspect: true,
            recocido: true
        },
        resultado: 'Ok',
        dateOfTest: new Date()
    },

];