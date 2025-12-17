/**
 * DTO for creating a broker
 * Maps to backend API contract for POST /api/brokers
 */
export interface CreateBrokerDto {
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
}
