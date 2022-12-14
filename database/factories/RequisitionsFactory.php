<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Requisitions>
 */
class RequisitionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => 2,
            'maker' => 'John Dee',
            'priority' => 'Normal',
            'description' => 'testingggg',
            'status' => 'Pending',
            'approval_count' => 0
        ];
    }
}
