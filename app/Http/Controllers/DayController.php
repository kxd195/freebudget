<?php

namespace App\Http\Controllers;

use App\Day;
use Illuminate\Http\Request;
use App\Budget;

class DayController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'budget_id' => 'required',
            'actualdate' => 'required|date',
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
    public function create() {
        return $this->edit(0, intval(request('budget_id')));
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
    public function edit($id, $budget_id = 0) {
        $entry = $id !== 0 ? Day::findOrFail($id) : new Day();
        
        if ($budget_id !== 0) {
            $entry->budget_id = $budget_id;
            $entry->budget = Budget::find($entry->budget_id);
        }
        
        return view('days.edit', ['entry' => $entry]);
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
        
        $existing_day = Day::where([
            ['budget_id', $request->get('budget_id')],
            ['actualdate', $request->get('actualdate')]
        ])->first();
        
        // conflict found, moving all person entries to new date
        if (isset($existing_day) && ($existing_day->id != $id || $id == 0)) {
            if ($id != 0) {
                $entry = Day::findOrFail($id);

                // move all person entries to new day
                foreach ($entry->people as $person) {
                    $person->day_id = $existing_day->id;
                    $person->disableVersioning();
                    $person->save();
                }

                $this->destroy($id, true);
            }
            
            $showDayId = $existing_day->id;
            $showBudgetId = $existing_day->budget_id;
        } else {
            $entry = $id !== 0 ? Day::findOrFail($id) : new Day($request->all());
            $entry->disableVersioning();
            $success = $id !== 0 ? $entry->update($request->all()) : $entry->save();
            if (!$success) {
                return redirect()->back()
                        ->with('message-danger', 'Something wrong happened while saving')
                        ->withInput();
            }
            
            $showDayId = $entry->id;
            $showBudgetId = $entry->budget_id;
        }
        
        // $this->renameDays($showBudgetId);

        return redirect()->to(route('budgets.show', $showBudgetId) . '#day-' . $showDayId)
                ->with('message', 'Your changes has been successfully saved!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $from_update = false) {
        $entry = Day::findOrFail($id);
        
        if (!$entry->delete()) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while deleting')
                    ->withInput();
        }
        
        if ($from_update)
            return;
        
        // $this->renameDays($entry->budget_id);
        
        return redirect()->route('budgets.show', $entry->budget_id)
                ->with('message-warning', $entry->generateName() . ' has been successfully deleted!');
    }
    
    private function renameDays($budget_id) {
        $counter = 1;
        foreach (Day::all()->where('budget_id', $budget_id)->sortBy('actualdate') as $day) {
            $day->name = $counter;
            $day->disableVersioning();
            $day->save();
            $counter++;
        }
    }
}
