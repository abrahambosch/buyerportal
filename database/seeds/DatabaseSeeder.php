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
            'first_name' => 'Bob',
            'last_name' => 'Boxter',
            'email' => 'Bob@Burlington.com',
            'password' => bcrypt('welcome'),
            'company' => 'Burlington',
            'user_type' => 'buyer'
        ]);

        $seller = User::create([
            'first_name' => 'Brittany',
            'last_name' => '',
            'email' => 'Brittany@Jiahome.us',
            'password' => bcrypt('welcome'),
            'company' => 'Jiahome',
            'user_type' => 'seller'
        ]);

        BuyerSeller::create(['buyer_id' => $buyer->id, 'seller_id' => $seller->id]);
        
    }
}
