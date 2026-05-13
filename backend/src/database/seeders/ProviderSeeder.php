<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'fantasy_name' => 'Distribuidora Norte',
                'business_name' => 'Distribuidora Norte S.A.',
                'cuit' => '30-12345678-9',
                'iibb' => '901-123456-7',
                'tax_status' => 'Responsable Inscripto',
                'agreement' => 'Convenio Multilateral',
                'phone_1' => '+54 11 4444-5555',
                'phone_2' => '+54 11 4444-5556',
                'email_1' => 'contacto@distribuidoranorte.com',
                'email_2' => 'ventas@distribuidoranorte.com',
                'address' => 'Av. Córdoba 1500, CABA, Argentina',
                'website' => 'https://distribuidoranorte.com',
                'contact_name' => 'María González',
                'observations' => 'Proveedor principal de materiales de construcción',
                'business_hours_start' => '08:00',
                'business_hours_end' => '17:00',
            ],
            [
                'fantasy_name' => 'Tech Solutions',
                'business_name' => 'Tech Solutions Argentina S.R.L.',
                'cuit' => '30-98765432-1',
                'iibb' => '901-654321-8',
                'tax_status' => 'Responsable Inscripto',
                'agreement' => 'Convenio Multilateral',
                'phone_1' => '+54 11 5555-6666',
                'email_1' => 'info@techsolutions.com.ar',
                'address' => 'Av. Santa Fe 2500, CABA, Argentina',
                'website' => 'https://techsolutions.com.ar',
                'contact_name' => 'Carlos Rodríguez',
                'observations' => 'Proveedor de equipamiento informático',
                'business_hours_start' => '09:00',
                'business_hours_end' => '18:00',
            ],
            [
                'fantasy_name' => 'Alimentos del Sur',
                'business_name' => 'Alimentos del Sur Sociedad Anónima',
                'cuit' => '30-11223344-5',
                'iibb' => '901-112233-4',
                'tax_status' => 'Responsable Inscripto',
                'agreement' => 'Convenio Multilateral',
                'phone_1' => '+54 11 6666-7777',
                'phone_2' => '+54 11 6666-7778',
                'phone_3' => '+54 11 6666-7779',
                'email_1' => 'ventas@alimentosdelsur.com',
                'email_2' => 'administracion@alimentosdelsur.com',
                'address' => 'Av. Belgrano 3000, CABA, Argentina',
                'website' => 'https://alimentosdelsur.com',
                'contact_name' => 'Ana Martínez',
                'observations' => 'Distribuidor mayorista de alimentos',
                'business_hours_start' => '07:00',
                'business_hours_end' => '16:00',
            ],
        ];

        foreach ($providers as $provider) {
            Provider::create($provider);
        }
    }
}

