<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MysteryBox;
use Carbon\Carbon;

class MysteryBoxSeeder extends Seeder
{
    public function run(): void
    {
        $budgets = [25000, 50000, 75000, 100000, 125000, 150000];
        $moods = ['Happy', 'Romantic', 'Mysterious', 'Brave', 'Calm', 'Funny'];

        // Map mood ke nama file gambar
        $imageMap = [
            'Romantic'   => 'mysterybox_pink.png',
            'Funny'      => 'mysterybox_biru.png',
            'Calm'       => 'mysterybox_hijau.png',
            'Mysterious' => 'mysterybox_ungu.png',
            'Brave'      => 'mysterybox_merah.png',
            'Happy'      => 'mysterybox_kuning.png',
        ];

        foreach ($budgets as $budget) {
            foreach ($moods as $mood) {
                MysteryBox::create([
                    'name'       => 'Mystery Box',
                    'budget'     => $budget,
                    'mood'       => $mood,
                    'image'      => $imageMap[$mood] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
