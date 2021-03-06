<?php

namespace App\Http\Controllers;

use App\Show;

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
        $list = Show::all()->sortBy('updated_at desc, name');
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
