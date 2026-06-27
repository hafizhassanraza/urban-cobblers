<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name' => 'Urban Cobblers',
            'store_email' => 'admin@urbancobblers.com',
            'store_phone' => '+1 555-0100',
            'shipping_rate' => '15.00',
            'low_stock_threshold' => '5',
            'currency_symbol' => '$',
        ];

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
