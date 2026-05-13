export interface DiametersWithResistanceResponse {
    success: boolean;
    data: {
        name: string;
        description: string;
        decimal_value: number;
        iram_copper_ohm_max_resistance: number;
    }[];
    message: string;
}