<?php

namespace App\Http\Controllers;

use App\Show;
use Illuminate\Http\Request;

class ShowController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('shows.list', ['list' => Show::all()->sortBy('name')]);
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
        return view('shows.show', ['entry' => Show::findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $entry = $id !== 0 ? Show::findOrFail($id) : new Show();
        return view('shows.edit', ['entry' => $entry]);
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
        
        $entry = $id !== 0 ? Show::findOrFail($id) : new Show($request->all());
        $success = $id !== 0 ? $entry->update($request->all()) : $entry->save();
        if (!$success) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while saving')
                    ->withInput();
        }
        
        return redirect()->route('shows.index')
                ->with('message', 'Your changes has been successfully saved!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $entry = Show::find($id);
        if (!$entry->delete()) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while deleting')
                    ->withInput();
        }
        
        return redirect()->route('home')
                ->with('message-warning', 'The show "' . $entry->name . '" has been successfully deleted!');
    }
}
