<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        City::truncate();
        Schema::enableForeignKeyConstraints();
        $array =
            array(
                array(
                    'id' => 1,
                    'name' => 'Karachi',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 2,
                    'name' => 'Lahore',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 3,
                    'name' => 'Faisalabad',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 4,
                    'name' => 'Rawalpindi',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 5,
                    'name' => 'Gujranwala',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 6,
                    'name' => 'Peshawar',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 7,
                    'name' => 'Multan',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 8,
                    'name' => 'Saidu Sharif',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 9,
                    'name' => 'Hyderabad City',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 10,
                    'name' => 'Islamabad',
                    'province_id' => 156,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 11,
                    'name' => 'Quetta',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 12,
                    'name' => 'Bahawalpur',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 13,
                    'name' => 'Sargodha',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 14,
                    'name' => 'Sialkot City',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 15,
                    'name' => 'Sukkur',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 16,
                    'name' => 'Larkana',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 17,
                    'name' => 'Chiniot',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 18,
                    'name' => 'Shekhupura',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 19,
                    'name' => 'Jhang City',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 20,
                    'name' => 'Dera Ghazi Khan',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 21,
                    'name' => 'Gujrat',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 22,
                    'name' => 'Rahimyar Khan',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 23,
                    'name' => 'Kasur',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 24,
                    'name' => 'Mardan',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 25,
                    'name' => 'Mingaora',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 26,
                    'name' => 'Nawabshah',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 27,
                    'name' => 'Sahiwal',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 28,
                    'name' => 'Mirpur Khas',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 29,
                    'name' => 'Okara',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 30,
                    'name' => 'Mandi Burewala',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 31,
                    'name' => 'Jacobabad',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 32,
                    'name' => 'Saddiqabad',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 33,
                    'name' => 'Kohat',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 34,
                    'name' => 'Muridke',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 35,
                    'name' => 'Muzaffargarh',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 36,
                    'name' => 'Khanpur',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 37,
                    'name' => 'Gojra',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 38,
                    'name' => 'Mandi Bahauddin',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 39,
                    'name' => 'Abbottabad',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 40,
                    'name' => 'Turbat',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 41,
                    'name' => 'Dadu',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 42,
                    'name' => 'Bahawalnagar',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 43,
                    'name' => 'Khuzdar',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 44,
                    'name' => 'Pakpattan',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 45,
                    'name' => 'Tando Allahyar',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 46,
                    'name' => 'Ahmadpur East',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 47,
                    'name' => 'Vihari',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 48,
                    'name' => 'Jaranwala',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 49,
                    'name' => 'New Mirpur',
                    'province_id' => 145,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 50,
                    'name' => 'Kamalia',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 51,
                    'name' => 'Kot Addu',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 52,
                    'name' => 'Nowshera',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 53,
                    'name' => 'Swabi',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 54,
                    'name' => 'Khushab',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 55,
                    'name' => 'Dera Ismail Khan',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 56,
                    'name' => 'Chaman',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 57,
                    'name' => 'Charsadda',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 58,
                    'name' => 'Kandhkot',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 59,
                    'name' => 'Chishtian',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 60,
                    'name' => 'Hasilpur',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 61,
                    'name' => 'Attock Khurd',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 62,
                    'name' => 'Muzaffarabad',
                    'province_id' => 145,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 63,
                    'name' => 'Mianwali',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 64,
                    'name' => 'Jalalpur Jattan',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 65,
                    'name' => 'Bhakkar',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 66,
                    'name' => 'Zhob',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 67,
                    'name' => 'Dipalpur',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 68,
                    'name' => 'Kharian',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 69,
                    'name' => 'Mian Channun',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 70,
                    'name' => 'Bhalwal',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 71,
                    'name' => 'Jamshoro',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 72,
                    'name' => 'Pattoki',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 73,
                    'name' => 'Harunabad',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 74,
                    'name' => 'Kahror Pakka',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 75,
                    'name' => 'Toba Tek Singh',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 76,
                    'name' => 'Samundri',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 77,
                    'name' => 'Shakargarh',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 78,
                    'name' => 'Sambrial',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 79,
                    'name' => 'Shujaabad',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 80,
                    'name' => 'Hujra Shah Muqim',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 81,
                    'name' => 'Kabirwala',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 82,
                    'name' => 'Mansehra',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 83,
                    'name' => 'Lala Musa',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 84,
                    'name' => 'Chunian',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 85,
                    'name' => 'Nankana Sahib',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 86,
                    'name' => 'Bannu',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 87,
                    'name' => 'Pasrur',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 88,
                    'name' => 'Timargara',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 89,
                    'name' => 'Parachinar',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 90,
                    'name' => 'Chenab Nagar',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 91,
                    'name' => 'Gwadar',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 92,
                    'name' => 'Abdul Hakim',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 93,
                    'name' => 'Hassan Abdal',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 94,
                    'name' => 'Tank',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 95,
                    'name' => 'Hangu',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 96,
                    'name' => 'Risalpur Cantonment',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 97,
                    'name' => 'Karak',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 98,
                    'name' => 'Kundian',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 99,
                    'name' => 'Umarkot',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 100,
                    'name' => 'Chitral',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 101,
                    'name' => 'Dainyor',
                    'province_id' => 134,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 102,
                    'name' => 'Kulachi',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 103,
                    'name' => 'Kalat',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 104,
                    'name' => 'Kotli',
                    'province_id' => 145,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 105,
                    'name' => 'Gilgit',
                    'province_id' => 134,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 106,
                    'name' => 'Narowal',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 107,
                    'name' => 'Khairpur Mir',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 108,
                    'name' => 'Khanewal',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 109,
                    'name' => 'Jhelum',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 110,
                    'name' => 'Haripur',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 111,
                    'name' => 'Shikarpur',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 112,
                    'name' => 'Rawala Kot',
                    'province_id' => 145,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 113,
                    'name' => 'Hafizabad',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 114,
                    'name' => 'Lodhran',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 115,
                    'name' => 'Malakand',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 116,
                    'name' => 'Attock City',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 117,
                    'name' => 'Batgram',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 118,
                    'name' => 'Matiari',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 119,
                    'name' => 'Ghotki',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 120,
                    'name' => 'Naushahro Firoz',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 121,
                    'name' => 'Alpurai',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 122,
                    'name' => 'Bagh',
                    'province_id' => 145,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 123,
                    'name' => 'Daggar',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 124,
                    'name' => 'Leiah',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 125,
                    'name' => 'Tando Muhammad Khan',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 126,
                    'name' => 'Chakwal',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 127,
                    'name' => 'Badin',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 128,
                    'name' => 'Lakki',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 129,
                    'name' => 'Rajanpur',
                    'province_id' => 1,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 130,
                    'name' => 'Dera Allahyar',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 131,
                    'name' => 'Shahdad Kot',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 132,
                    'name' => 'Pishin',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 133,
                    'name' => 'Sanghar',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 134,
                    'name' => 'Upper Dir',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 135,
                    'name' => 'Thatta',
                    'province_id' => 37,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 136,
                    'name' => 'Dera Murad Jamali',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 137,
                    'name' => 'Kohlu',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 138,
                    'name' => 'Mastung',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 139,
                    'name' => 'Dasu',
                    'province_id' => 67,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 140,
                    'name' => 'Athmuqam',
                    'province_id' => 145,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 141,
                    'name' => 'Loralai',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 142,
                    'name' => 'Barkhan',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 143,
                    'name' => 'Musa Khel Bazar',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 144,
                    'name' => 'Ziarat',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 145,
                    'name' => 'Gandava',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 146,
                    'name' => 'Sibi',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 147,
                    'name' => 'Dera Bugti',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 148,
                    'name' => 'Eidgah',
                    'province_id' => 134,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 149,
                    'name' => 'Uthal',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 150,
                    'name' => 'Khuzdar',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 151,
                    'name' => 'Chilas',
                    'province_id' => 134,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 152,
                    'name' => 'Panjgur',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 153,
                    'name' => 'Gakuch',
                    'province_id' => 134,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 154,
                    'name' => 'Qila Saifullah',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 155,
                    'name' => 'Kharan',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 156,
                    'name' => 'Aliabad',
                    'province_id' => 134,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 157,
                    'name' => 'Awaran',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),

                array(
                    'id' => 158,
                    'name' => 'Dalbandin',
                    'province_id' => 101,
                    'created_at' => '2023-02-25 13:09:42',
                    'updated_at' => '2023-02-25 13:09:42'
                ),
            );

        foreach ($array as $row) {
            City::create([
                'name' => $row['name'],
                'province_id' => $row['province_id'],
            ]);
        }


    }
}
