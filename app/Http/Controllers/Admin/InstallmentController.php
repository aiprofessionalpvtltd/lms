<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Installment;
use App\Models\InstallmentDetail;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-installments'])->only(['index', 'show', 'view']);
        $this->middleware(['permission:create-installments']);
        $this->middleware(['permission:edit-installments'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-installments'])->only('destroy');
    }

    public function index()
    {
        $title = 'installments';
        $installments = Installment::all();
        return view("admin.installment.index", compact('installments', 'title'));
    }

    public function view($id)
    {
        $installment = Installment::with(['details', 'user', 'loanApplication' ,'recoveries'])->findOrFail($id);
//        dd($installment);
        return view("admin.installment.view", compact('installment'));
    }


    public function updateDueDate(Request $request, $id)
    {
        $request->validate([
            'due_date' => 'required|date',
        ]);
        $installmentDetail = InstallmentDetail::findOrFail($id);
        $installmentDetail->due_date = $request->due_date;
        $installmentDetail->save();

        return response()->json(['message' => 'Due date updated successfully.'],200);
    }

}
