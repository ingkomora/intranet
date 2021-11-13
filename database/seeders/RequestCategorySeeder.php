<?php

namespace Database\Seeders;

use App\Models\RequestCategory;
use App\Models\RequestCategoryType;
use Illuminate\Database\Seeder;

class RequestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RequestCategoryType::updateOrCreate(
            [
                'id' => 1
            ],
            [
                'name' => 'Članstvo',
                'status_id' => 1,
                'created_at' => '2021-11-12 11:26:00.000000',
                'updated_at' => now()
            ]
        );
        RequestCategoryType::updateOrCreate(
            [
                'id' => 2
            ],
            [
                'name' => 'Registar',
                'status_id' => 1,
                'created_at' => '2021-11-12 11:26:00.000000',
                'updated_at' => now()
            ]
        );

        RequestCategory::updateOrCreate(
            [
                'id' => 1
            ],
            [
                'name' => 'Prijem u članstvo',
                'request_category_type_id' => 1,
                'status_id' => 1,
                'created_at' => '2021-11-12 11:26:00.000000',
                'updated_at' => now()
            ]
        );
        RequestCategory::updateOrCreate(
            [
                'id' => 2
            ],
            [
                'name' => 'Prekid članstva',
                'request_category_type_id' => 1,
                'status_id' => 1,
                'created_at' => '2021-11-12 11:26:00.000000',
                'updated_at' => now()
            ]
        );
    }
}
