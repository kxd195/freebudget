<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Budget;
use App\Unit;
use App\BudgetVersion;

class BudgetController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'startdate' => 'date',
        ]);
    }
    
    public function tagVersion(Request $request, $id) {
        $budget = Budget::findOrFail($id);
        $budget->tagVersion($request->input('name'));
        return redirect()->route('budgets.show', $id)
                ->with('message', 'Version successfully tagged!');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('budgets.list', ['list' => Budget::all()->sortBy('name')]);
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
    public function show($id, $from_share = false, $read_only = false, Request $request) {
        $budget = Budget::findOrFail($id);
        $budget->days = $budget->days->sortBy('actualdate');

        // add the day name and actualdate to a new column called display_name
        $budget->days->map(function($i) {
            $i['display_name'] = $i->generateName();
            return $i;
        });
            
        $days = $budget->days->pluck('display_name', 'id');
            
        foreach ($budget->days as $day) {
            $day->people = $day->people->sortBy(function($i) { 
                return strtoupper($i->unit->name . '-' . $i->scene . '-' . $i->description);
            }); 
            
        }
        
        $units = Unit::all();
        $scenes = $budget->getScenes();
        $printer_friendly = $request->get('pf');

        if ($printer_friendly)
            return view('budgets.print', ['budget' => $budget, 'units' => $units, 'days' => $days, 'scenes' => $scenes, 'from_share' => $from_share, 'readonly' => $read_only]);

        return view('budgets.show', ['budget' => $budget, 'units' => $units, 'days' => $days, 'scenes' => $scenes, 'from_share' => $from_share, 'readonly' => $read_only]);
    }
    
    public function showVersion($id, $version, $from_share = false) {
        $budget_version = BudgetVersion::findOrFail($version)->init();

        $budget = $budget_version->getBudget();
        $budget->version_info = $budget_version; 
        $budget->days = $budget->days->sortBy('actualdate');
        
        foreach ($budget->days as $day) {
            $day->people = $day->people->sortBy(function($i) {
                return strtoupper($i->unit->name . '-' . $i->scene . '-' . $i->description);
            });
                
        }
        
        $units = Unit::all();
        return view('budgets.show', ['budget' => $budget, 'units' => $units, 'from_share' => $from_share, 'readonly' => true]);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $entry = $id !== 0 ? Budget::findOrFail($id) : new Budget();
        
        if (intval(request()->get('show_id')) !== 0)
            $entry->show_id = intval(request()->get('show_id'));
        
        return view('budgets.edit', ['entry' => $entry]);
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
        
        $isCreateRequest = $id === 0;
        
        $entry = !$isCreateRequest ? Budget::findOrFail($id) : new Budget($request->all());
        $entry->disableVersioning();
        $success = !$isCreateRequest ? $entry->update($request->all()) : $entry->save();

        if (!$success) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while saving')
                    ->withInput();
        }
        
        // only new creations will auto-create days
        if ($isCreateRequest && isset($entry->startdate) && (isset($entry->enddate) || $entry->num_days > 0)) {
            $entry->autoCreate();
        }
        
        return redirect()->route('budgets.show', $entry->id)
                ->with('message', 'Your changes has been successfully saved!');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $entry = Budget::find($id);
        
        if (!$entry->delete()) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while deleting')
                    ->withInput();
        }
        
        return redirect()->route('home')
                ->with('message-warning', 'The budget "' . $entry->name . '" has been successfully deleted!');
    }
}
