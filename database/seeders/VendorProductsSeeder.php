<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\VendorProduct;

class VendorProductsSeeder extends Seeder
{
    public function run()
    {
        $vendors = [
            'Evee' => [
                'Electric Bike List',
                'Flipper',
                'Evee Nisa',
                'Evee Gen-Z',
                'Evee Nisa 3W',
            ],
            'Road Prince' => [
                'Fuel Bike List',
                'RP-70CC',
                'RP-70CC(Alloy Rim)',
                'RP-70CC(Passion Plus)',
                'RP-110CC(Jackpot)',
            ],
            'Midea' => [
                'Midea Dc Inverter',
                'WALL MOUNTED DC INVERTER R-410 HEAT & COOL',
                'MSAGB-12HRFN',
                'MSAGB-18HRFN',
                'MSAGB-24HRFN',
            ],
            'Ez bike' => [
                'Electric Bike List',
                'Ez Bolt',
                'EZ Electron',
            ],
            'United' => [
                'Electric Bike List',
                'United Revolt Electric 1,500 Watt (Lithium Battery)',
                'United Bullet Electric 2,000 Watt (Lithium Battery)',
                'United Scotty Sharp Electric 1,000 Watt (Lithium Battery)',
                'United Scotty Smart Electric 1,000 Watt (Graphene Battery)',
            ],
            'Mobile phones' => [
                'Mobile Phones List',
                'Samsung Galaxy Z Fold 5',
                'Samsung Galaxy Z Fold 6',
                'Samsung Galaxy S24 Ultra 512GB',
            ],
            'Yadea bikes' => [
                'RUIBIN',
                'T5',
                'G5',
                'EPOC H',
            ],
            'Honda' => [
                'Honda',
                'CD 70',
                'CD 70 Dream',
                'PRIDOR',
                'CG 125',
            ],
            'Yamaha' => [
                'Yamaha',
                'YBR 125',
                'YBR 125G',
                'YB125Z-DX',
                'YB125Z',
            ],
            'Boss' => [
                'Boss Washing Machine',
                'KE 777-C-Green (12kgs)',
                'KE 3000-N-15-BS-Green (12kgs)',
                'KE 1500-BS-Green (10kgs)',
                'KE 2000 Green (9kgs)',
            ],
            'Dawlance' => [
                'Dawlance Refrigerators',
                'Dawlance Microwave Ovens',
                'Dawlance Washing Machines',
                'Dawlance Air Conditioners',
            ],
            'TCL' => [
                'TCL LED TVs',
                'TCL QLED TVs',
                'TCL Sound Bars',
                'TCL Smart Air Conditioners',
            ],
            'Samsung' => [
                'Samsung QLED TVs',
                'Samsung Smart Refrigerators',
                'Samsung Washing Machines',
                'Samsung Microwave Ovens',
            ],
            'Gaming Consoles' => [
                'Sony PlayStation',
                'Microsoft Xbox',
            ],
            'Smartwatches' => [
                'Apple Watch',
                'Samsung Galaxy Watch',
                'Garmin Watch',
            ],
            'Dell' => [
                'Inspiron Series',
            ],
            'HP' => [
                'Pavilion Series',
            ],
            'Lenovo' => [
                'ThinkPad Series',
            ],
            'Apple' => [
                'MacBook Pro',
                'MacBook Air',
            ],
        ];

        foreach ($vendors as $vendorName => $products) {
            $vendor = Vendor::firstOrCreate(['name' => $vendorName]);

            foreach ($products as $productName) {
                VendorProduct::firstOrCreate([
                    'vendor_id' => $vendor->id,
                    'product_name' => $productName,
                ]);
            }
        }
    }
}
