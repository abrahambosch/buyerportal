<?php

use Illuminate\Database\Seeder;
use App\User;
use App\BuyerSeller;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);
        $buyer = User::create([
            'first_name' => 'Abraham',
            'last_name' => 'Bosch',
            'email' => 'abrahambosch@hotmail.com',
            'password' => bcrypt('welcome'),
            'company' => 'Clothing Boutique',
            'user_type' => 'buyer'
        ]);

        $seller = User::create([
            'first_name' => 'Dr',
            'last_name' => 'B',
            'email' => 'sales@burlington.com',
            'password' => bcrypt('welcome'),
            'company' => 'Burlington',
            'user_type' => 'seller'
        ]);

        BuyerSeller::create(['buyer_id' => $buyer->id, 'seller_id' => $seller->id]);
        
    }
}
