<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-expenses'])->only(['index', 'show']);
        $this->middleware(['permission:create-expenses'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-expenses'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Expense Categories';
        $categories = ExpenseCategory::all();
        return view('admin.expense-category.index', compact('title', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create Expense Category';
        return view('admin.expense-category.create', compact('title'));
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
            'name' => 'required|unique:expense_categories,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category = ExpenseCategory::create([
            'name' => $request->input('name'),
        ]);

        if ($category) {
            return redirect()->route('show-expense-categories')->with('success', 'Expense Category created successfully.');
        } else {
            return redirect()->route('show-expense-categories')->with('error', 'Something went wrong.');
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
        $title = 'Expense Category Details';
        $category = ExpenseCategory::find($id);
        return view('admin.expense-category.show', compact('title', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Expense Category';
        $category = ExpenseCategory::find($id);
        return view('admin.expense-category.edit', compact('title', 'category'));
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
            'name' => 'required|unique:expense_categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category = ExpenseCategory::find($id);
        $category->name = $request->input('name');
        $category->save();

        if ($category) {
            return redirect()->route('show-expense-categories')->with('success', 'Expense Category updated successfully.');
        } else {
            return redirect()->route('show-expense-categories')->with('error', 'Something went wrong.');
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
        $category = ExpenseCategory::find($request->id);
        $category->delete();

        return response()->json(['success' => 'Expense Category deleted successfully.']);
    }
}
