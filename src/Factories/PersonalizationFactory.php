<?php

namespace BildVitta\SpVendas\Factories;

use BildVitta\SpProduto\Models\RealEstateDevelopment\Unit;
use BildVitta\SpVendas\Models\Personalization;
use BildVitta\SpVendas\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class PersonalizationFactory.
 *
 * @package BildVitta\SpVendas\Factories
 */
class PersonalizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Personalization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        /** @var Sale $sale */
        $sale = Sale::inRandomOrder()->first();
        
        return [
            'uuid' => fake()->uuid(),
            'description' => fake()->text(),
            'file' => null,
            'value' => null,
            'type' => null,
            'sale_id' => $sale,
            'unit_id' => $sale->unit,
        ];
    }
}
