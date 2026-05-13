/**
 * DTO for creating a provider
 * Maps to backend API contract
 */
export interface CreateProviderDto {
    business_name: string;
    fantasy_name?: string;
    cuit: string;
    iibb?: string;
    tax_status_id?: number;
    agreement_id?: number;
    category_id?: number;
    broker_id?: number | null;
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
    // Broker data (for creating broker together with provider)
    broker_first_name?: string;
    broker_last_name?: string;
    broker_email?: string;
    broker_phone?: string;
}

