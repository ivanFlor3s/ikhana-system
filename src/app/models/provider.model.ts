export interface Provider {
  id: number;
  fantasy_name: string;
  business_name: string;
  cuit: string;
  iibb: string;
  tax_status: string;
  agreement: string;
  phone_1: string;
  phone_2: string | null;
  email_1: string;
  address: string;
  website: string;
  contact_name: string;
  observations: string;
  business_hours_start: string;
  business_hours_end: string;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
}
