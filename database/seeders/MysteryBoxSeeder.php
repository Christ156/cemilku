<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MysteryBox;
use App\Models\Snack;

class MysteryBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID snack yang sudah ada dari database berdasarkan nama mereka
        $snackMarshmallowYouka = Snack::where('name', 'Marshmallow Youka')->firstOrFail();
        $snackBiskuatCoklatSmall = Snack::where('name', 'Biskuat Coklat Small')->firstOrFail();
        $snackOreoSoftCake = Snack::where('name', 'Oreo Soft Cake')->firstOrFail();
        $snackBiskuatOriginalSmall = Snack::where('name', 'Biskuat Original Small')->firstOrFail();
        $snackBiskuatOriginalMedium = Snack::where('name', 'Biskuat Original Medium')->firstOrFail();
        $snackTopStroberi = Snack::where('name', 'Top Stroberi')->firstOrFail();
        $snackKraftKejuCake = Snack::where('name', 'Kraft Keju Cake')->firstOrFail();
        $snackDilanSmallPack = Snack::where('name', 'Dilan Small Pack')->firstOrFail();
        $snackTopChocolate = Snack::where('name', 'Top Chocolate')->firstOrFail();
        $snackBengBengSmall = Snack::where('name', 'Beng Beng Small')->firstOrFail();
        $snackBengBengMedium = Snack::where('name', 'Beng Beng Medium')->firstOrFail();
        $snackTopTripleChoco = Snack::where('name', 'Top Triple Choco')->firstOrFail();
        $snackNextarStroberi = Snack::where('name', 'Nextar Stroberi')->firstOrFail();
        $snackNextarPineapple = Snack::where('name', 'Nextar Pineapple')->firstOrFail();
        $snackTangoVanilla = Snack::where('name', 'Tango Vanilla')->firstOrFail();
        $snackKalpa = Snack::where('name', 'Kalpa')->firstOrFail();
        $snackTangoChocolate = Snack::where('name', 'Tango Chocolate')->firstOrFail();
        $snackGoodTime = Snack::where('name', 'Good Time')->firstOrFail();
        $snackOreoVanilla = Snack::where('name', 'Oreo Vanilla')->firstOrFail();
        $snackOreoChocolate = Snack::where('name', 'Oreo Chocolate')->firstOrFail();
        $snackUltraMilkStroberi125ml = Snack::where('name', 'Ultra Milk Stroberi 125ml')->firstOrFail();
        $snackUltraMilkFullCream125ml = Snack::where('name', 'Ultra Milk Full Cream 125ml')->firstOrFail();
        $snackUltraMilkChocolate125ml = Snack::where('name', 'Ultra Milk Chocolate 125ml')->firstOrFail();
        $snackUltraMilkSariKacangHijau125ml = Snack::where('name', 'Ultra Milk Sari Kacang Hijau 125ml')->firstOrFail();
        $snackBuavitaJeruk125ml = Snack::where('name', 'Buavita Jeruk 125ml')->firstOrFail();
        $snackBuavitaJambu125ml = Snack::where('name', 'Buavita Jambu 125ml')->firstOrFail();
        $snackBuavitaAppple125ml = Snack::where('name', 'Buavita Appple 125ml')->firstOrFail();
        $snackGreenfieldsFullCream105ml = Snack::where('name', 'Greenfields Full Cream 105ml')->firstOrFail();
        $snackGreenfieldsStroberi105ml = Snack::where('name', 'Greenfields Stroberi 105ml')->firstOrFail();
        $snackGreenfieldsChocolate105ml = Snack::where('name', 'Greenfields Chocolate 105ml')->firstOrFail();
        $snackPiattosSapiPanggang = Snack::where('name', 'Piattos Sapi Panggang')->firstOrFail();
        $snackChikiBalls = Snack::where('name', 'Chiki Balls')->firstOrFail();
        $snackPiattosSambalMatah = Snack::where('name', 'Piattos Sambal Matah')->firstOrFail();
        $snackChitatoSapiPanggang = Snack::where('name', 'Chitato Sapi Panggang')->firstOrFail();
        $snackChitatoKeju = Snack::where('name', 'Chitato Keju')->firstOrFail();
        $snackCheetosJagungBakarKeju = Snack::where('name', 'Cheetos Jagung Bakar Keju')->firstOrFail();
        $snackCheetosCheddarCheese = Snack::where('name', 'Cheetos Cheddar Cheese')->firstOrFail();
        $snackQtelaBarbeque = Snack::where('name', 'Qtela Barbeque')->firstOrFail();
        $snackLaysRumputLaut = Snack::where('name', 'Lays Rumput Laut')->firstOrFail();
        $snackJetzChocoFies = Snack::where('name', 'Jetz Choco Fies')->firstOrFail();

        // Data for Mystery Boxes
        $mysteryBoxesData = [
            [
                'budget' => 25000.00,
                'mood' => 'Happy',
                'stock' => 150,
                'snacks' => [
                    ['snack_id' => $snackOreoSoftCake->id, 'quantity' => 2],
                    ['snack_id' => $snackTopStroberi->id, 'quantity' => 3],
                    ['snack_id' => $snackDilanSmallPack->id, 'quantity' => 1],
                ]
            ],
            [
                'budget' => 50000.00,
                'mood' => 'Romantic',
                'stock' => 100,
                'snacks' => [
                    ['snack_id' => $snackKraftKejuCake->id, 'quantity' => 2],
                    ['snack_id' => $snackNextarStroberi->id, 'quantity' => 2],
                    ['snack_id' => $snackTangoVanilla->id, 'quantity' => 1],
                    ['snack_id' => $snackUltraMilkStroberi125ml->id, 'quantity' => 1],
                ]
            ],
            [
                'budget' => 75000.00,
                'mood' => 'Mysterious',
                'stock' => 198,
                'snacks' => [
                    ['snack_id' => $snackChitatoKeju->id, 'quantity' => 1],
                    ['snack_id' => $snackPiattosSambalMatah->id, 'quantity' => 1],
                    ['snack_id' => $snackJetzChocoFies->id, 'quantity' => 2],
                    ['snack_id' => $snackUltraMilkChocolate125ml->id, 'quantity' => 1],
                    ['snack_id' => $snackGreenfieldsChocolate105ml->id, 'quantity' => 1],
                ]
            ],
            [
                'budget' => 100000.00,
                'mood' => 'Brave',
                'stock' => 178,
                'snacks' => [
                    ['snack_id' => $snackChikiBalls->id, 'quantity' => 1],
                    ['snack_id' => $snackCheetosJagungBakarKeju->id, 'quantity' => 1],
                    ['snack_id' => $snackPiattosSapiPanggang->id, 'quantity' => 1],
                    ['snack_id' => $snackUltraMilkFullCream125ml->id, 'quantity' => 2],
                    ['snack_id' => $snackGreenfieldsFullCream105ml->id, 'quantity' => 2],
                ]
            ],
            [
                'budget' => 125000.00,
                'mood' => 'Calm',
                'stock' => 195,
                'snacks' => [
                    ['snack_id' => $snackOreoVanilla->id, 'quantity' => 2],
                    ['snack_id' => $snackTangoVanilla->id, 'quantity' => 2],
                    ['snack_id' => $snackGoodTime->id, 'quantity' => 2],
                    ['snack_id' => $snackUltraMilkSariKacangHijau125ml->id, 'quantity' => 2],
                    ['snack_id' => $snackBuavitaJambu125ml->id, 'quantity' => 1],
                    ['snack_id' => $snackGreenfieldsStroberi105ml->id, 'quantity' => 1],
                ]
            ],
            [
                'budget' => 150000.00,
                'mood' => 'Funny',
                'stock' => 121,
                'snacks' => [
                    ['snack_id' => $snackMarshmallowYouka->id, 'quantity' => 5],
                    ['snack_id' => $snackBiskuatCoklatSmall->id, 'quantity' => 3],
                    ['snack_id' => $snackBengBengMedium->id, 'quantity' => 2],
                    ['snack_id' => $snackKalpa->id, 'quantity' => 2],
                    ['snack_id' => $snackLaysRumputLaut->id, 'quantity' => 1],
                    ['snack_id' => $snackBuavitaJeruk125ml->id, 'quantity' => 2],
                    ['snack_id' => $snackBuavitaAppple125ml->id, 'quantity' => 1],
                ]
            ],
        ];

        foreach ($mysteryBoxesData as $boxData) {
            $snacksToAttach = $boxData['snacks'];
            unset($boxData['snacks']);

            $mysteryBox = MysteryBox::create($boxData);

            foreach ($snacksToAttach as $snackItem) {
                $mysteryBox->snacks()->attach($snackItem['snack_id'], ['quantity' => $snackItem['quantity']]);
            }
        }
    }
}
