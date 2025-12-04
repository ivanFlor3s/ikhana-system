export interface Provider {
  id: number;
  fantasy_name: string;
  business_name: string;
  cuit: string;
  iibb: string;
  tax_status_id: number;
  agreement_id: number;
  category_id: number;
  broker_id: number | null;
  phone_1: string;
  phone_2: string | null;
  phone_3: string | null;
  phone_4: string | null;
  phone_5: string | null;
  email_1: string;
  email_2: string | null;
  email_3: string | null;
  email_4: string | null;
  email_5: string | null;
  address: string;
  website: string;
  contact_name: string | null;
  observations: string;
  business_hours_start: string;
  business_hours_end: string;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;

  // Nested objects from API
  tax_status?: {
    id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
  };
  agreement?: {
    id: number;
    code: string;
    name: string;
    description: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
  };
  category?: {
    id: number;
    name: string;
    description: string;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
  };
  broker?: any | null;
}

