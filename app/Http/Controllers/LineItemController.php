<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\LineItem;
use App\RateClass;
use App\Unit;

class LineItemController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'day_id' => 'required',
            'unit_id' => 'required',
            'rate_class_id' => 'required',
            'description' => 'required',
            'qty' => 'required|numeric|min:1',
            'hours' => 'required|numeric',
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return $this->edit(0, request('day_id'));
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
    public function edit($id, $day_id = null) {
        $entry = $id !== 0 ? LineItem::findOrFail($id) : new LineItem();
        
        if ($day_id !== null)
            $entry->day_id = $day_id;
        
        $categories = Category::with('rate_classes')->get()->sortBy('name');
        $days = $entry->day->budget->days->sortBy('name');
        
        // add the day name and actualdate to a new column called display_name 
        $days->map(function($i) {
            $i['display_name'] = $i->name . (isset($i->actualdate) ? " (" . $i->actualdate->format('D, M j, Y') . ")" : "");
            return $i;
        });
        
        $days = $days->pluck('display_name', 'id');
        $units = Unit::all()->pluck('name', 'id');
        return view('line_items.edit', ['entry' => $entry, 'categories' => $categories, 
            'days' => $days, 'units' => $units]);
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
        
        $entry = $id !== 0 ? LineItem::findOrFail($id)->fill($request->all()) : new LineItem($request->all());
        $entry->disableVersioning();
        
        if (!$entry->save()) {
            return redirect()->route('budgets.show', $request->budget_id)
                    ->with('message-danger', 'Something wrong happened while saving')
                    ->withInput();
        }
        
        return redirect()->route('budgets.show', $request->budget_id)
                ->with('message', 'Your changes has been successfully saved!')
                ->with('show_day_id', $entry->day_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $entry = LineItem::findOrFail($id);
        
        if (!$entry->delete()) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while deleting')
                    ->withInput();
        }
        
        return redirect()->route('budgets.show', $entry->day->budget_id)
                ->with('message-warning', 'The item has been successfully deleted!')
                ->with('show_day_id', $entry->day_id);
    }
}
