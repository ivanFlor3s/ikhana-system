import { CreateProviderCommand } from '@generated/provider-api.service';
import { CreateProviderDto } from '../dtos/create-provider.dto';
import { ProviderFormData } from '../form-data-models/provider-form-data.model';

export function mapProviderFormToDto(formData: ProviderFormData): CreateProviderCommand {
    const formatTime = (hours: number, minutes: number): string => {
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    };

    return {
        businessName: formData.socialReason,
        fantasyName: formData.name,
        cuit: formData.cuit,
        brokerIds: [],
        businessHoursStart: formData.since ? formatTime(formData.since.hours, formData.since.minutes) : null,
        businessHoursEnd: formData.to ? formatTime(formData.to.hours, formData.to.minutes) : null,
        iibb: formData.iib || null,
        taxStatusId: formData.ivaPositionId || null,
        agreementId: formData.convenioId || null,
        categoryIds: [formData.categoryId] ,
        address: formData.address || null,
        website: formData.website || null,
        observations: formData.observations || null,
        email: formData.email || null,
        phone: formData.phone || null,
        contactName: null,
    };
}

