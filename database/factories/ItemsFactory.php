<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items>
 */
class ItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $qty = rand(0, 25);
        $price = rand(0, 1000);
        $items = ['Long bondpaper', 'Short bondpaper', 'Envelope'];
        return [
            'item' => $items[rand(0, 2)],
            'units' => 'box,rim,pack',
            'qty' => $qty,
            'price' => $price,
            'supplier' => fake()->company(),
            'worth' => $qty * $price
        ];
    }
}
