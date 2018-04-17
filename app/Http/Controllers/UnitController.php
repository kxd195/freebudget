<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unit;

class UnitController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'name' => 'required',
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('units.list', ['list' => Unit::all()->sortBy('name')]);
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
        return UnitController::edit($id);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $entry = $id !== 0 ? Unit::findOrFail($id) : new Unit();
        return view('units.edit', ['entry' => $entry]);
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
        
        $entry = $id !== 0 ? Unit::findOrFail($id) : new Unit($request->all());
        $success = $id !== 0 ? $entry->update($request->all()) : $entry->save();
        if (!$success) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while saving')
                    ->withInput();
        }
        
        return redirect()->route('units.index')
                ->with('message', 'Your changes has been successfully saved!');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!Unit::destroy($id)) {
            return redirect()->back()
            ->with('message-danger', 'Something wrong happened while deleting')
            ->withInput();
        }
        
        return redirect()->route('units.index')
                ->with('message-warning', 'Unit has been successfully deleted!');
    }
}
