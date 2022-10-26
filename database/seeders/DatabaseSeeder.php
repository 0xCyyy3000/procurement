<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Items;
use App\Models\SavedItems;
use App\Models\Requisitions;
use Illuminate\Database\Seeder;
use App\Models\InventoryCategories;
use App\Models\UserSavedItems;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123'),
            'department' => 'Admin'
        ]);

        User::factory()->create([
            'name' => 'John Dee',
            'email' => 'john@gmail.com',
            'password' => bcrypt('123'),
            'department' => 'Accounting'
        ]);

        User::factory()->create([
            'name' => 'Karen Doe',
            'email' => 'karen@gmail.com',
            'password' => bcrypt('123'),
            'department' => 'Information Technology'
        ]);

        Requisitions::factory(5)->create();
        Items::factory(3)->create();
        SavedItems::factory()->create([
            'user_id' => 2,
            'item_id' => 1,
            'item' => 'Long bondpaper',
            'unit' => 'rim',
            'qty' => 100
        ]);
        SavedItems::factory()->create([
            'user_id' => 2,
            'item_id' => 1,
            'item' => 'Short bondpaper',
            'unit' => 'rim',
            'qty' => 100
        ]);

        InventoryCategories::factory(1)->create();
        UserSavedItems::factory(4)->create();
    }
}
