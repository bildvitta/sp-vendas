<?php

namespace BildVitta\SpVendas\Factories;

use BildVitta\SpVendas\Models\RealEstateAgency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class RealEstateAgencyFactory.
 *
 * @package BildVitta\SpVendas\Factories
 */
class RealEstateAgencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RealEstateAgency::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->name,
            'company_name' => fake()->company,
            'document' => fake()->cnpj,
            'creci' => fake()->numerify('#########'),
            'ie' => fake()->numerify('#########'),
            'postal_code' => fake()->postcode,
            'address' => fake()->streetName,
            'street_number' => fake()->buildingNumber,
            'city' => fake()->city,
            'state' => fake()->state,
            'complement' => fake()->secondaryAddress,
            'neighborhood' => fake()->cityPrefix,
            'email' => fake()->unique()->safeEmail,
            'phone' => fake()->phoneNumber,
            'phone_two' => fake()->phoneNumber,
            'representative_name' => fake()->name,
            'representative_nationality' => fake()->city,
            'representative_occupation' => fake()->words(3, true),
            'representative_document' => fake()->cpf,
            'representative_rg' => fake()->numerify('#########'),
            'representative_two_name' => fake()->name,
            'representative_two_nationality' => fake()->city,
            'representative_two_occupation' => fake()->words(3, true),
            'representative_two_document' => fake()->cpf,
            'representative_two_rg' => fake()->numerify('#########'),
            'external_code' => fake()->numberBetween(3, 999),
            'hub_company_id' => config('sp-vendas.model_company')::inRandomOrder()->first()?->id,
        ];
    }
}
