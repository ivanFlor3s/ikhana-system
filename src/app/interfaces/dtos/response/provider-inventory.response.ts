export interface ProviderInventoryResponse {
    success: boolean;
    data: ProviderInventory;
    message: string;

}

export interface ProviderInventory {
    provider_id: number;
    coils_count: number;
}