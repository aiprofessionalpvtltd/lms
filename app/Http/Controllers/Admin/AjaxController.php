<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\LoanApplicationResource;
use App\Models\City;
use App\Models\District;
use App\Models\EmployerDemand;
use App\Models\InternshipRoleLevel;
use App\Models\LevelFour;
use App\Models\LevelThree;
use App\Models\LevelTwo;
use App\Models\LoanApplication;
use App\Models\Province;
use App\Models\VendorProduct;
use Illuminate\Http\Request;

class AjaxController extends Controller
{

    public function getProvinceByCountryAjax(Request $request)
    {
        $responseData = Province::select('id', 'name')->where('country_id', '=', $request->countryID)->orderBy('name', 'ASC')->get();
        return response()->json($responseData);
    }

    public function getDistrictByProvinceAjax(Request $request)
    {
        $responseData = District::select('id', 'name')->where('province_id', '=', $request->provinceID)->orderBy('name', 'ASC')->get();
        return response()->json($responseData);
    }

    public function getCityByProvinceAjax(Request $request)
    {
        $responseData = City::select('id', 'name')->where('province_id', '=', $request->provinceID)->orderBy('name', 'ASC')->get();
        return response()->json($responseData);
    }

    public function getVendorProductByVendor(Request $request)
    {
        $responseData = VendorProduct::select('id', 'product_name')->where('vendor_id', '=', $request->vendorID)->orderBy('product_name', 'ASC')->get();
        return response()->json($responseData);
    }

    public function getApplicationByCustomer(Request $request)
    {
        $responseData = LoanApplication::select('id', 'application_id')->where('user_id', '=', $request->customerID)->orderBy('application_id', 'ASC')->get();
        return response()->json($responseData);
    }

}
