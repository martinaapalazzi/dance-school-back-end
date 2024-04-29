<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Models
use App\Models\Restaurant;
use App\Models\User;

// Helpers
use Illuminate\Support\Facades\Schema;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Restaurant::truncate();
        });

        $allRestaurants = config('restaurants.allRestaurants');

        foreach ($allRestaurants as $key => $singleRestaurant) {

            // Ottieni tutti gli ID utente non già associati a un ristorante
            $availableUserIds = User::whereNotIn('id', Restaurant::pluck('user_id')->toArray())->pluck('id');

            // Se non ci sono più ID utente disponibili, interrompi il processo di creazione dei ristoranti
            if ($availableUserIds->isEmpty()) {
                break;
            }

            // Seleziona casualmente un ID utente disponibile
            $randomUserId = $availableUserIds->random();

            $vat = '';

            for($i = 0; $i < 11; $i++){
                $vat .= rand(0, 9);
            };

            $restaurant = Restaurant::create([
                'company_name' => $singleRestaurant['name'],
                'slug' => str()->slug($singleRestaurant['name']),
                'address' => fake()->address(),
                'vat_number' => $vat,
                'img' => $singleRestaurant['img'],
                'visible' => fake()->boolean(),
                'user_id' => $randomUserId
            ]);
            
        }

    }
}
