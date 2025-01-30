<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-expenses'])->only(['index', 'show']);
        $this->middleware(['permission:create-expenses'])->only(['create', 'store']);
        $this->middleware(['permission:edit-expenses'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-expenses'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Expenses';
        $expenses = Expense::with('category')->get(); // Load the related category
        return view('admin.expense.index', compact('title', 'expenses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create Expense';
        $categories = ExpenseCategory::all(); // Fetch all categories for the dropdown
        return view('admin.expense.create', compact('title', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $expense = Expense::create([
            'expense_category_id' => $request->input('expense_category_id'),
            'date' => $request->input('date'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
        ]);

        if ($expense) {
            return redirect()->route('show-expense')->with('success', 'Expense created successfully.');
        } else {
            return redirect()->route('show-expense')->with('error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Expense Details';
        $expense = Expense::with('category')->find($id); // Load the related category
        return view('admin.expense.show', compact('title', 'expense'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Expense';
        $expense = Expense::find($id);
        $categories = ExpenseCategory::all(); // Fetch all categories for the dropdown
//        dd($expense);
        return view('admin.expense.edit', compact('title', 'expense', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $expense = Expense::find($id);
        $expense->expense_category_id = $request->input('expense_category_id');
        $expense->date = $request->input('date');
        $expense->amount = $request->input('amount');
        $expense->description = $request->input('description');
        $expense->save();

        if ($expense) {
            return redirect()->route('show-expense')->with('success', 'Expense updated successfully.');
        } else {
            return redirect()->route('show-expense')->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $expense = Expense::find($request->id);
        $expense->delete();

        return response()->json(['success' => 'Expense deleted successfully.']);
    }
}
