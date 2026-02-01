
export interface CreateEntryResponse {
    success: boolean;
    data: {
        id: number;
        remito: string;
        status: string;
        test: {
            result: string;
        };
    };
    message: string;
}