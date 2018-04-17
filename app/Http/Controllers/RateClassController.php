<?php

namespace App\Http\Controllers;

use App\Category;
use App\RateClass;
use Illuminate\Http\Request;

class RateClassController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'category_id' => 'required',
            'name' => 'required',
            'code' => 'required',
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('rate_classes.list', ['list' => RateClass::all()->sortBy(function($item) {
            return $item->category->name . $item->name;
        })]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->edit(0);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return $this->update($request, 0);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return $this->edit($id);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $entry = $id !== 0 ? RateClass::findOrFail($id) : new RateClass();
        $categories = Category::all()->sortBy('name')->pluck('name', 'id');
        return view('rate_classes.edit', ['entry' => $entry, 'categories' => $categories, 'bgcolors' => RateClass::$bgcolors]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $this->run_validation($request);
        
        $entry = $id !== 0 ? RateClass::findOrFail($id) : new RateClass($request->all());
        $success = $id !== 0 ? $entry->update($request->all()) : $entry->save();
        if (!$success) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while saving')
                    ->withInput();
        }
        
        return redirect()->route('rate_classes.index')
                ->with('message', 'Your changes has been successfully saved!');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $entry = RateClass::find($id);
        if (!$entry->delete()) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while deleting')
                    ->withInput();
        }
        
        return redirect()->route('rate_classes.index')
                ->with('message-warning', 'The rate class "' . $entry->name . '" has been successfully deleted!');
    }
}
