<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Provider;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_name' => $this->faker->company(),
            'fantasy_name' => $this->faker->companySuffix(),
            'cuit' => $this->faker->unique()->numerify('##-########-#'),
            'iibb' => $this->faker->numerify('###-######-#'),
            // Optional FKs can be null or created if needed, keeping them null for base test to avoid recursion
            'tax_status_id' => null,
            'agreement_id' => null,
            'category_id' => null,
            'broker_id' => null,
            'email_1' => $this->faker->safeEmail(),
            'phone_1' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
        ];
    }
}
