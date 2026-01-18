export interface IngresoCobre {
    fecha: Date;
    remito: string;
    provider: string;
    weight: string;
    diameter: number;
    lote: string;
    validations: {
        embalaje: boolean;
        superficialAspect: boolean;
        recocido: boolean;
    }
    resultado: string;
    dateOfTest: Date;
}