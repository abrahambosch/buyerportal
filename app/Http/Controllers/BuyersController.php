<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class BuyersController extends Controller
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
        return view('buyers/index');
    }

    /**
     * create buyer form
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('buyers/create');
    }



}
