<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserSavedItems>
 */
class UserSavedItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => 3,
            'items' => [
                [
                    'item_id' => 8,
                    'item_name' => 'Test item',
                    'item_unit' => 'reams',
                    'item_qty' => 16
                ]
            ]
        ];
    }
}
