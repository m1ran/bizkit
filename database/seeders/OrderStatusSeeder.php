<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'draft',
                'label' => 'Draft',
                'sort_order' => 10,
            ],
            [
                'name' => 'pending',
                'label' => 'Pending',
                'sort_order' => 20,
            ],
            [
                'name' => 'paid',
                'label' => 'Paid',
                'sort_order' => 30,
            ],
            [
                'name' => 'shipped',
                'label' => 'Shipped',
                'sort_order' => 40,
            ],
            [
                'name' => 'finished',
                'label' => 'Finished',
                'sort_order' => 50,
            ],
            [
                'name' => 'cancelled',
                'label' => 'Cancelled',
                'sort_order' => 60,
            ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::firstOrCreate($status);
        }
    }
}
