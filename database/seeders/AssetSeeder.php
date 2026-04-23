<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        if (Asset::count() >= 50) {
            return;
        }

        $makes = ['Dell', 'HP', 'Lenovo', 'Apple', 'Asus', 'Acer', 'Samsung', 'Cisco', 'Ubiquiti'];
        $types = ['Laptop', 'Desktop', 'Monitor', 'Smartphone', 'Tablet', 'Server', 'Switch', 'Router', 'Docking Station'];
        $statuses = [
            'InStock', 'InStock', 'InStock', 'InStock', // weighted
            'Allocated', 'Allocated',
            'Maintenance',
            'Retired',
        ];

        $assets = [];

        for ($i = 1; $i <= 55; $i++) {
            $make   = $makes[array_rand($makes)];
            $type   = $types[array_rand($types)];
            $status = $statuses[array_rand($statuses)];
            $sn     = strtoupper(substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 8));
            $tag    = 'IT-' . date('Y') . '-' . strtoupper(substr(str_replace('-', '', (string) \Illuminate\Support\Str::uuid()), 0, 6));

            $name = match ($type) {
                'Laptop'       => $make === 'Apple' ? 'MacBook Pro 14"' : "{$make} ThinkPad/Latitude",
                'Smartphone'   => $make === 'Apple' ? 'iPhone 14' : "{$make} Galaxy/Pixel",
                'Monitor'      => "{$make} 27\" 4K Display",
                'Server'       => "{$make} PowerEdge 2U",
                'Switch'       => "{$make} 24-Port PoE Switch",
                default        => "{$make} {$type} Standard",
            };

            $assets[] = [
                'tag_number'    => $tag,
                'name'          => $name,
                'specifications' => "Standard {$make} {$type} configuration.\nS/N: {$sn}",
                'purchase_date' => now()->subDays(rand(0, 1000))->toDateString(),
                'status'        => $status,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        // Bulk insert — observer does NOT fire for insertions via query builder
        // We use Eloquent create loop so the observer fires for each asset.
        // But for seeding we skip the observer to avoid 55 log entries — use DB directly.
        \Illuminate\Support\Facades\DB::table('assets')->insert($assets);
    }
}
