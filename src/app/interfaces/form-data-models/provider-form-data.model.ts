/**
 * Form data structure from the provider creation/edit dialog
 */
export interface ProviderFormData {
    name: string;
    cuit: string;
    iib: string;
    address: string;
    socialReason: string;
    ivaPositionId: number;
    convenioId: number;
    categoryId: number;
    website: string;
    phone: string;
    otherPhones: string[];
    email: string;
    otherEmails: string[];
    observations: string;
    since: {
        hours: number;
        minutes: number;
    };
    to: {
        hours: number;
        minutes: number;
    };
    // Broker data
    brokerFirstName: string;
    brokerLastName: string;
    brokerEmail: string;
    brokerPhone: string;
}

