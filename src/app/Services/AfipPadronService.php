<?php

namespace App\Services;

use Afip;

class AfipPadronService
{
    protected $afip;

    public function __construct()
    {
        $config = [
            'CUIT'        => config('afip.cuit'),
            'production'  => config('afip.production'),
        ];

        // If using AFIPSDK service (recommended for easy integration)
        if (config('afip.access_token')) {
            $config['access_token'] = config('afip.access_token');
            
            // Load certificate content if files exist (optional with access_token)
            $certPath = config('afip.cert');
            $keyPath = config('afip.key');
            
            if (file_exists($certPath) && file_exists($keyPath)) {
                $config['cert'] = file_get_contents($certPath);
                $config['key'] = file_get_contents($keyPath);
                if (config('afip.passphrase')) {
                    $config['passphrase'] = config('afip.passphrase');
                }
            }
        } else {
            // Using own certificates (load content)
            $certPath = config('afip.cert');
            $keyPath = config('afip.key');
            
            if (!file_exists($certPath) || !file_exists($keyPath)) {
                throw new \Exception('AFIP certificates not found. Please configure AFIP_ACCESS_TOKEN or provide certificate files.');
            }
            
            $config['cert'] = file_get_contents($certPath);
            $config['key'] = file_get_contents($keyPath);
            $config['passphrase'] = config('afip.passphrase');
        }

        $this->afip = new \Afip($config);
    }

    public function taxIdExists(string $taxId): bool
    {
        if (!$this->isValidTaxIdFormat($taxId)) {
            \Log::warning('Invalid tax ID format', ['tax_id' => $taxId]);
            return false;
        }

        try {
            $cleanTaxId = preg_replace('/[^0-9]/', '', $taxId);
            \Log::info('Consulting AFIP for tax ID', ['tax_id' => $cleanTaxId]);
            
            $data = $this->afip->RegisterScopeFour->GetTaxpayerDetails((int)$cleanTaxId);
            
            \Log::info('AFIP Response', ['data' => $data, 'is_empty' => empty($data)]);
            
            return !empty($data);
        } catch (\Throwable $e) {
            \Log::error('AFIP API Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return false;
        }
    }

    protected function isValidTaxIdFormat(string $taxId): bool
    {
        // Remove non-numeric characters
        $taxId = preg_replace('/[^0-9]/', '', $taxId);

        if (strlen($taxId) !== 11) {
            return false;
        }

        // CUIT/CUIL validation algorithm
        $coefficients = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$taxId[$i] * $coefficients[$i];
        }

        $remainder = $sum % 11;
        $verificationDigit = 11 - $remainder;
        
        if ($verificationDigit === 11) {
            $verificationDigit = 0;
        }
        if ($verificationDigit === 10) {
            $verificationDigit = 9;
        }

        return (int)$taxId[10] === $verificationDigit;
    }
}
