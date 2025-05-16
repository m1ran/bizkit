<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            'Вінницька',
            'Волинська',
            'Дніпропетровська',
            'Донецька',
            'Житомирська',
            'Закарпатська',
            'Запорізька',
            'Івано-Франківська',
            'Київська',
            'Кіровоградська',
            'Луганська',
            'Львівська',
            'Миколаївська',
            'Одеська',
            'Полтавська',
            'Рівненська',
            'Сумська',
            'Тернопільська',
            'Харківська',
            'Херсонська',
            'Хмельницька',
            'Черкаська',
            'Чернівецька',
            'Чернігівська',
            'м. Київ',
            'м. Севастополь',
            'АР Крим',
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(['name' => $region]);
        }
    }
}
