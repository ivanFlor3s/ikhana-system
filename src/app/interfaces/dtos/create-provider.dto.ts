/**
 * DTO for creating a provider
 * Maps to backend API contract
 */
export interface CreateProviderDto {
    business_name: string;
    fantasy_name?: string;
    cuit: string;
    iibb?: string;
    tax_status?: string;
    agreement?: string;
    phone_1?: string;
    phone_2?: string;
    email_1?: string;
    email_2?: string;
    address?: string;
    website?: string;
    contact_name?: string;
    observations?: string;
    business_hours_start?: string;
    business_hours_end?: string;
}
