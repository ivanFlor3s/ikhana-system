export interface LoginResponseDto {
    success: boolean;
    data: {
        user: {
            id: number;
            name: string;
            email: string;
            email_verified_at: string | null;
            role_id: number;
            created_at: string;
            updated_at: string;
            role: {
                id: number;
                name: string;
                description: string;
                created_at: string;
                updated_at: string;
            };
        };
        token: string;
    };
    message: string;
}