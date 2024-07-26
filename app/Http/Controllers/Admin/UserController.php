<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use pdf;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Middlewares\PermissionMiddleware;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:view-users'])->only(['index', 'show']);
        $this->middleware(['permission:edit-users'])->only(['edit', 'update', 'resetID', 'changePassword', 'change']);
        $this->middleware(['permission:create-users'])->only(['create', 'store']);
        $this->middleware(['permission:delete-users'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $data = array();
        $title = 'Add User';
        $users = User::with('roles')
            ->where('id', '!=', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('backend.user.index', compact('title', 'users', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::where('id', '!=', 1)->get();
        $title = 'Add User';
        return view('backend.user.create', compact('roles', 'title'));
    }

    /**
     * Store a newly created resource in storage.composer
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'role_id' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required'

        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();

        }
        $validatedData = $validator->validated();

        $data['email_verified_at'] = Carbon::now();
        $data['password'] = $request->password;


        $user = User::create(array_merge($validatedData, $data));

        if ($request->filled('role_id')) {
            $user->syncRoles($request->input('role_id'));
        }


        return redirect()->route('show-user')->with('success', 'User Created Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit User';
        $user = User::with('roles')->find($id);
        $roles = Role::where('id', '!=', 1)->get();
        return view('backend.user.edit', compact('title', 'user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validatedData = $validator->validated();

            if ($request->filled('password')) {
                $validatorPassword = Validator::make($request->all(), [
                    'password' => 'required|min:8|confirmed',
                    'password_confirmation' => 'required'
                ]);

                if ($validatorPassword->fails()) {
                    return redirect()->back()
                        ->withErrors($validatorPassword)
                        ->withInput();
                }

                $validatedData['password'] = $request->input('password');
            }

            $user->update($validatedData);
            $user->roles()->detach();

            if ($request->has('role_id')) {
                $user->syncRoles($request->input('role_id'));
            }

            return redirect()->route('show-user')->with('success', 'User Updated Successfully');
        } catch (\Throwable $e) {
//            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function changeStatus(Request $request)
    {
        $user = User::find($request->id);
        $user->status = $request->status;
        $user->save();

        return response()->json(['success' => 'Status has been changed.']);
    }


    public function changePassword(Request $request)
    {

        $user = User::findOrFail($request->id);

        /*
        * Validate all input fields
        */
        $this->validate($request, [
            'new_password' => 'required',
            'again_password' => 'same:new_password',
        ]);

        if ($request->new_password != null) {
            $newPassword = $request->new_password;
            $user->fill(['password' => $newPassword])->save();

            return redirect()->back()->with(['success' => "Password Changed successfully"]);

        } else {
            return redirect()->back()->with(['info' => "There was error in changing password, please try again"]);
        }

    }


}
