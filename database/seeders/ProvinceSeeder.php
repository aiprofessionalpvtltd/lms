<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = array(
	array(
		"Id" => 1,
		"GeolocationType" => 4,
		"Code" => "Punjab",
		"Name" => "پنجاب",
		"IsActive" => 1,
		"SortOrder" => 20,
		"CreatedBy" => "Admin",
		"CreatedDate" => "00:00.0",
		"Latitude" => "NULL",
		"Longitude" => "NULL"),
	array(
		"Id" => 37,
		"GeolocationType" => 4,
		"Code" => "Sindh",
		"Name" => "سندھ",
		"IsActive" => 1,
		"SortOrder" => 30,
		"CreatedBy" => "Admin",
		"CreatedDate" => "00:00.0",
		"Latitude" => "NULL",
		"Longitude" => "NULL"),
	array(
		"Id" => 67,
		"GeolocationType" => 4,
		"Code" => "KPK",
		"Name" => "خیبر پختونخواہ",
		"IsActive" => 1,
		"SortOrder" => 50,
		"CreatedBy" => "Admin",
		"CreatedDate" => "00:00.0",
		"Latitude" => "NULL",
		"Longitude" => "NULL"),
	array(
		"Id" => 101,
		"GeolocationType" => 4,
		"Code" => "Balochistan",
		"Name" => "بلوچستان",
		"IsActive" => 1,
		"SortOrder" => 80,
		"CreatedBy" => "Admin",
		"CreatedDate" => "00:00.0",
		"Latitude" => "NULL",
		"Longitude" => "NULL"),
	array(
		"Id" => 134,
		"GeolocationType" => 4,
		"Code" => "Gilgit–Baltistan",
		"Name" => "گلگت بلتستان",
		"IsActive" => 1,
		"SortOrder" => 90,
		"CreatedBy" => "Admin",
		"CreatedDate" => "00:00.0",
		"Latitude" => "NULL",
		"Longitude" => "NULL"),
	array(
		"Id" => 145,
		"GeolocationType" => 4,
		"Code" => "AJK",
		"Name" => "آزاد جموں کشمیر",
		"IsActive" => 1,
		"SortOrder" => 100,
		"CreatedBy" => "Admin",
		"CreatedDate" => "00:00.0",
		"Latitude" => "NULL",
		"Longitude" => "NULL"),
	array(
		"Id" => 156,
		"GeolocationType" => 4,
		"Code" => "Federal",
		"Name" => "فیڈرل",
		"IsActive" => 1,
		"SortOrder" => 1,
		"CreatedBy" => "Admin",
		"CreatedDate" => "00:00.0",
		"Latitude" => "NULL",
		"Longitude" => "NULL")
);

        foreach($arr as $a=>$province){
            Province::create([
                'id' => $province['Id'],
                'name' => $province['Code'],
                'urdu' => $province['Name'],
            ]);
        }//end of foreach

    }
}
