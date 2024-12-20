<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Product;
use App\Models\Province;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function show(Request $request)
    {
        $title = 'All Products';

        // Handle AJAX request for DataTables
        if ($request->ajax()) {
            // Query to get products with their related province and district
            $products = Product::with('province', 'district')->orderBy('created_at', 'DESC');

            return DataTables::of($products)
                ->addColumn('vendor', function ($product) {
                    return $product->vendor ? $product->vendor->name : 'N/A'; // Handle case where province might not exist
                })
                ->addColumn('vendorProduct', function ($product) {
                    return $product->vendorProduct ? $product->vendorProduct->product_name : 'N/A'; // Handle case where province might not exist
                })
                ->addColumn('province', function ($product) {
                    return $product->province ? $product->province->name : 'N/A'; // Handle case where province might not exist
                })
                ->addColumn('district', function ($product) {
                    return $product->district ? $product->district->name : 'N/A'; // Handle case where district might not exist
                })
                ->addColumn('actions', function ($product) {
                    $actions = '';
                    if (auth()->user()->can('edit-products')) {
                        $actions .= '<a title="Edit" href="' . route('edit-product', $product->id) . '" class="text-primary mr-1"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('delete-products')) {
                        $actions .= '<a href="javascript:void(0)" data-url="' . route('destroy-product') . '" data-status="0" data-label="delete" data-id="' . $product->id . '" class="text-danger mr-1 change-status-record" title="Suspend Record"><i class="fas fa-trash"></i></a>';
                    }
                    return '<div class="d-flex">' . $actions . '</div>';
                })
                ->rawColumns(['actions']) // Render raw HTML in the actions column
                ->make(true);
        }

        LogActivity::addToLog('Products Listing View');

        // Return view for non-AJAX request
        return view('admin.product.index', compact('title'));
    }

    public function index()
    {
        $provinces = Province::all();
        $vendors = Vendor::all();
        $title = 'Add Products';
        return view('admin.product.create', compact('title', 'provinces','vendors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'detail' => 'nullable|string',
            'processing_fee' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'province_id' => 'exists:provinces,id',
            'district_id' => 'exists:districts,id',
            'vendor_id' => 'exists:vendors,id',
            'vendor_product_id' => 'exists:vendor_products,id',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create($data);
            DB::commit();

            LogActivity::addToLog('Product '.$request->name.' Created');


            return redirect()->route('show-product')->with('success', 'Product created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Product creation failed');
        }
    }

    public function edit($id)
    {
        $title = 'Edit Product';
        $product = Product::with('province', 'district')->find($id);
        $provinces = Province::all();
        $districts = District::where('province_id', $product->province_id)->get();

        $vendors = Vendor::all();
        $vendorProducts = VendorProduct::where('vendor_id',$product->vendor_id)->get();


        return view('admin.product.edit', compact('title', 'product', 'provinces', 'districts' ,'vendors','vendorProducts'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'required|numeric',
            'detail' => 'nullable|string',
            'processing_fee' => 'sometimes|required|numeric|min:0',
            'interest_rate' => 'sometimes|required|numeric|min:0',
            'province_id' => 'sometimes',
            'district_id' => 'sometimes',
            'vendor_id' => 'exists:vendors,id',
            'vendor_product_id' => 'exists:vendor_products,id',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return redirect()->route('show-product')->with('error', 'product not found.');
        }

        DB::beginTransaction();

        try {
            $product->update($data);
            LogActivity::addToLog('Product '.$request->name.' Updated');

            DB::commit();

            return redirect()->route('show-product')->with('success', 'Product updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Product update failed');
        }
    }

    public function destroy(Request $request)
    {
         $product = Product::find($request->id);

        if (!$product) {
            return response()->json(['error' => 'product not found.']);
         }



        DB::beginTransaction();

        try {
            $product->delete();
            LogActivity::addToLog('Product '.$product->name.' Deleted');

            DB::commit();
            return response()->json(['success' => 'Product Deleted Successfully']);

         } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Product deletion failed');
        }
    }
}
