<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Department;
use App\Models\Inventories;
use App\Models\User;
use App\Models\Items;
use App\Models\SavedItems;
use App\Models\Requisitions;
use Illuminate\Database\Seeder;
use App\Models\InventoryCategories;
use App\Models\ItemCategories;
use App\Models\SupplierItems;
use App\Models\Suppliers;
use App\Models\Units;
use App\Models\UserSavedItems;

use function PHPSTORM_META\map;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $departments = [
            'Branch Manager', 'School Director', 'Property Custodian',
            'SHS Department', 'IT Department', 'Gen Ed Department',
            'HM Department', 'Accountancy Department', 'Allied Health Department',
            'SHS Registrar', 'College Registrar', 'Admission', 'Accounting',
            'Technical', 'Library', 'Cashier', 'Not assigned'
        ];

        foreach ($departments as $department) {
            Department::create(['department' => $department]);
        }

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123'),
            'department' => 2
        ]);

        User::factory()->create([
            'name' => 'John Dee',
            'email' => 'john@gmail.com',
            'password' => bcrypt('123'),
            'department' => 13
        ]);

        User::factory()->create([
            'name' => 'Karen Doe',
            'email' => 'karen@gmail.com',
            'password' => bcrypt('123'),
            'department' => 5
        ]);

        User::factory()->create([
            'name' => 'Sonny Fischer',
            'email' => 'sonny@gmail.com',
            'password' => bcrypt('123'),
            'department' => 3
        ]);

        $company = fake()->company();

        Suppliers::create([
            'company_name' => $company,
            'contact_person' => [
                'name' => 'John Doe',
                'email' => $company . '@mail.com',
                'phone' => '09123456789',
            ],
            'address' => fake()->address()
        ]);

        Items::create([
            'item' => 'Item 1'
        ]);


        ItemCategories::create([
            'category' => 'Category 1',
            'total_items' => rand(1, 1000)
        ]);

        Units::create([
            'unit_name' => 'pcs'
        ]);

        Units::create([
            'unit_name' => 'reams'
        ]);

        Units::create([
            'unit_name' => 'box'
        ]);

        Units::create([
            'unit_name' => 'dozen'
        ]);

        SupplierItems::create([
            'item_id' => 1,
            'unit_id' => 1,
            'price' => 0
        ]);
    }
}
