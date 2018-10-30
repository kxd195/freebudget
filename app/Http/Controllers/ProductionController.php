<?php

namespace App\Http\Controllers;

use App\Production;
use Illuminate\Http\Request;

class ProductionController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'type' => 'required',
            'qty' => 'numeric',
            'num_union' => 'numeric',
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        foreach (Production::getTypes() as $type)
            $list[$type] = Production::where('type', $type)->orderBy('name')->get();

        return view('productions.list', ['list' => $list]);
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
        return view('productions.show', ['entry' => Production::findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $entry = $id !== 0 ? Production::findOrFail($id) : new Production();
        return view('productions.edit', ['entry' => $entry, 'fullscreen' => true]);
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
        
        $entry = $id !== 0 ? Production::findOrFail($id) : new Production($request->all());
        $success = $id !== 0 ? $entry->update($request->all()) : $entry->save();
        if (!$success) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while saving')
                    ->withInput();
        }
        
        if ($id === 0)
            return redirect()->route('budgets.create', ['production_id' => $entry->id])
                    ->with('message', 'The production "' . $entry->name . '" has been successfully created!');

        return redirect()->route('productions.index')
                ->with('message', 'Your changes has been successfully saved!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $entry = Production::find($id);
        if (!$entry->delete()) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while deleting')
                    ->withInput();
        }
        
        return redirect()->route('home')
                ->with('message-warning', 'The production "' . $entry->name . '" has been successfully deleted!');
    }
}
