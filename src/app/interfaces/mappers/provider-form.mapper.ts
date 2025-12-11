import { CreateProviderDto } from '../dtos/create-provider.dto';
import { ProviderFormData } from '../form-data-models/provider-form-data.model';

/**
 * Maps provider form data to API DTO format
 */
export function mapProviderFormToDto(formData: ProviderFormData): CreateProviderDto {
    // Format time as HH:MM
    const formatTime = (hours: number, minutes: number): string => {
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    };

    return {
        business_name: formData.socialReason,
        fantasy_name: formData.name,
        cuit: formData.cuit,
        iibb: formData.iib || undefined,
        tax_status_id: formData.ivaPositionId || undefined,
        agreement_id: formData.convenioId || undefined,
        category_id: formData.categoryId || undefined,
        broker_id: null, // Null when creating a new broker with the provider
        phone_1: formData.phone,
        phone_2: formData.otherPhones.length > 0 ? formData.otherPhones[0] : undefined,
        email_1: formData.email,
        email_2: formData.otherEmails.length > 0 ? formData.otherEmails[0] : undefined,
        address: formData.address || undefined,
        website: formData.website || undefined,
        contact_name: undefined, // Not in the current form
        observations: formData.observations || undefined,
        business_hours_start: formatTime(formData.since.hours, formData.since.minutes),
        business_hours_end: formatTime(formData.to.hours, formData.to.minutes),
        // Broker data
        broker_first_name: formData.brokerFirstName || undefined,
        broker_last_name: formData.brokerLastName || undefined,
        broker_email: formData.brokerEmail || undefined,
        broker_phone: formData.brokerPhone || undefined,
    };
}

