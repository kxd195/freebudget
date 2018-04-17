<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GlobalSetting;

class GlobalSettingController extends Controller {
    public function run_validation(Request $request) {
        $this->validate($request, [
            'hours_overtime' => 'required|numeric|min:1',
            'multiplier_overtime' => 'required|numeric|min:1',
            'hours_double' => 'required|numeric|min:1',
            'multiplier_double' => 'required|numeric|min:1',
        ]);
    }
        
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $entry = GlobalSetting::findOrFail($id);
        return view('global_settings.edit', ['entry' => $entry]);
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
        
        $entry = GlobalSetting::findOrFail($id);
        if (!$entry->update($request->all())) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while saving')
                    ->withInput();
        }
        
        return redirect()->route('global_settings.edit', $entry->id)
                ->with('message', 'Your changes has been successfully saved!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
