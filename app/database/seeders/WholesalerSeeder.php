<?php

namespace Database\Seeders;

use App\Models\Wholesaler;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WholesalerSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Sligro',
            'Bidfood',
            'Hanos',
            'Makro',
            'De Kweker',
            'VHC Jongens',
        ];

        foreach ($names as $name) {
            Wholesaler::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_verified' => true]
            );
        }
    }
}
