<?php

namespace App\Http\Controllers;

use App\Production;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $list = Production::all()->sortBy('name');
        return view('home', ['list' => $list]);
    }
    
    /**
     * Display a listing of the system settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings() {
        return view('settings');
    }
}
