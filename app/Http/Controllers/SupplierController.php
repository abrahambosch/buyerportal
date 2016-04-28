<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = \App\User::where('user_type', 'supplier')->get();
        return view('supplier/index', ['suppliers' => $suppliers]);
    }

    /**
     * create buyer form
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('supplier/create');
    }


    /**
     * store new buyer
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $password = str_random(8);
        $password = "welcome";
        $credentials = $request->only('email', 'password', 'first_name', 'middle_name', 'last_name', 'company');
        $credentials['password'] = Hash::make($password);
        $credentials['user_type'] = "supplier";
        try {
            $supplier = User::create($credentials);
            Auth::user()->suppliers()->attach($supplier->id);
            //BuyerSupplierMap::create(['buyer_id' => Auth::id(), 'supplier_id' => $supplier->id]); // add record in buyer_supplier
            return redirect()->route('supplier.index')->with('status', 'Supplier created');
        } catch (Exception $e) {
            return back()->withInput();
            //echo "failed to create buyer:" . $e->getMessage() . "<br>";
            //return Response::json(['error' => 'User already exists.'], Illuminate\Http\Response::HTTP_CONFLICT);
        }

    }


    public function show(Request $request, $id)
    {
        $user = \App\User::findOrFail($id);
        return view('supplier/show', ['user' => $user]);
    }

    public function edit(Request $request, $id)
    {
        $user = \App\User::findOrFail($id);
        return view('supplier/edit', ['user' => $user]);
    }

    /**
     * store new buyer
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = \App\User::findOrFail($id);
        $password = $request->get("password");
        if (!empty($password)) {
            $user->password = Hash::make($password);
        }
        $user->email = $request->get("email");
        $user->first_name = $request->get("first_name");
        $user->middle_name = $request->get("middle_name");
        $user->last_name = $request->get("last_name");
        $user->company = $request->get("company");
        $user->user_type = $request->get("user_type", 'supplier');
        $user->save();

        return redirect()->route('supplier.index')->with('status', 'Supplier updated');
    }

    public function destroy(Request $request, $id)
    {
        $user = \App\User::findOrFail($id);
        $user->delete();
        return redirect()->route('supplier.index')->with('status', 'Supplier deleted');
    }

}
