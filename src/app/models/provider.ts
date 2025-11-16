import { Time } from "./hour";

export interface Provider {
    id: number;
    name: string;
    cuit: string;
    iib: string;
    address: string;
    socialReason: string;
    ivaPosition: string;
    convenio: string;
    website: string;


    phone: string;
    otherPhones: string[];

    email: string;
    otherEmails: string[];

    observations: string;

    since: Time
    to: Time

}