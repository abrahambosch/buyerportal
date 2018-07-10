<?php

use Illuminate\Database\Seeder;
use App\User;
use App\BuyerSupplier;

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
            'last_name' => 'Barker',
            'email' => 'BobBarker@Burlington.com',
            'password' => bcrypt('welcome'),
            'company' => 'Burlington',
            'user_type' => 'buyer'
        ]);

        $supplier = User::create([
            'first_name' => 'Joe',
            'last_name' => 'Supplier',
            'email' => 'sal@supplier-wholesale.com',
            'password' => bcrypt('welcome'),
            'company' => 'Joe Wholesale',
            'user_type' => 'supplier'
        ]);

        BuyerSupplier::create(['buyer_id' => $buyer->id, 'supplier_id' => $supplier->id]);
        
    }
}
