<?php

namespace App\Http\Controllers;

use App\Budget;
use App\Category;
use App\Day;
use App\Person;
use App\RateClass;
use App\Unit;
use Illuminate\Http\Request;
use App\LineItem;

class PersonController extends Controller {
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function run_validation(Request $request) {
        $this->validate($request, [
            'unit_id' => 'required',
            'description' => 'required',
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $entry = $id !== 0 ? Person::findOrFail($id) : new Person();
        
        if (request('day_id') !== null)
            $entry->day_id = request('day_id');

        if ($id === 0)
            $entry->scene = session('last_scene');
       
        $categories = Category::with('rate_classes')->get()->sortBy('name');
        $day_entries = $entry->budget->days->sortBy('actualdate');

        // add the day name and actualdate to a new column called display_name
        $day_entries->map(function($i) {
            $i['display_name'] = $i->generateName();
            return $i;
        });

        $days = $day_entries->pluck('display_name', 'id');

        $days->prepend("Undated Entries", '');

        $units = Unit::all()->pluck('name', 'id');
        $scenes = Budget::getScenesFromBudget($entry->budget->id);
        
        if (request('copy') !== null) {
            // copy the line-items first, set them as new items, and then put it back in the entry
            $line_items = $entry->line_items;
            foreach ($line_items as $item)
                $item->id = null;
            
            $entry->id = null;
            $entry->line_items = $line_items;
        }
        
        return view('people.edit', ['entry' => $entry, 'days' => $days, 'categories' => $categories, 'units' => $units, 'scenes' => $scenes]);
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
        
        $entry = $id !== 0 ? Person::findOrFail($id)->fill($request->all()) : new Person($request->all());
        
        if ($request->get('scene_option') === 'new')
            $entry->scene = $request->get('scene_new');
        else if ($request->get('scene_option') === 'none')
            $entry->scene  = null;
                
        session(['last_scene' => $entry->scene]);
        
        
        
        $day_ids = explode(',', $request->get('day_id'));
        
        $isFirstEntry = true;
        $added_line_items = array();

        foreach ($day_ids as $day_id) {
            $person = $isFirstEntry ? $entry : $entry->replicate();
            $person->day_id = empty($day_id) ? null : $day_id;
            $person->disableVersioning();
            $person->save();
            
            if ($isFirstEntry) {
                foreach ($request->get('line_items', array()) as $data) {
                    if (!isset($data['rate_class_id']))
                        continue;
                    
                    $item = isset($data['id']) ? LineItem::find($data['id']) : new LineItem();
                    $item->person_id = $person->id;
                    $item->fill($data);
                    
                    $rateclass = RateClass::findOrFail($item->rate_class_id);
                    if ($rateclass->code === 'NUT' && $item->cost_overridden
                            && $item->cost > $rateclass->rate) {
                        $item->rate_class_id = RateClass::where('code', 'NUHR')->first()->id;
                    }

                    $item->disableVersioning();
                    $item->save();
                    $added_line_items[] = $item->id;
                }

                // delete the line items that were removed
                foreach ($person->line_items as $item) {
                    $found = false;
                    foreach ($added_line_items as $added_id) {
                        if ($item->id === $added_id)
                            $found = true;
                    }
                    
                    if (!$found)
                        $item->delete();
                }
                
                $isFirstEntry = false;
            } else {
                // copy the line-items from the first save
                foreach ($added_line_items as $source_id) {
                    $new_item = LineItem::findOrFail($source_id)->replicate();
                    $new_item->person_id = $person->id;
                    $new_item->disableVersioning();
                    $new_item->save();
                }
            }
        }

        return redirect()->route('budgets.show', $request->budget_id)
                ->with('message', 'Your changes has been successfully saved!')
                ->with('show_day_id', $entry->day_id);
    }

    public function updateWholeScene($budget_id, Request $request) {
        $old_day_id = $request->get('old_day_id');
        $old_scene = $request->get('old_scene');
        $new_day_id = $request->get('day_id');
        $new_scene = $request->get('scene');
    
        if ($request->get('scene_option') === 'new')
            $new_scene = $request->get('scene_new');
        else if ($request->get('scene_option') === 'none')
            $new_scene = null;
            
        $day = Day::find($old_day_id);
        
        foreach ($day->people as $person) {
            if ($person->scene === $old_scene) {
                $person->day_id = $new_day_id;
                $person->scene = $new_scene;
                $person->disableVersioning();
                $person->save();
            }
        }
        
        return redirect()->route('budgets.show', $budget_id)
                ->with('message', 'Your changes has been successfully saved!')
                ->with('show_day_id', $new_day_id);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $entry = Person::findOrFail($id);
        
        if (!$entry->delete()) {
            return redirect()->back()
                    ->with('message-danger', 'Something wrong happened while deleting')
                    ->withInput();
        }
        
        return redirect()->route('budgets.show', $entry->day->budget_id)
                ->with('message-warning', 'The person "' . $entry->description . '" has been successfully deleted!')
                ->with('show_day_id', $entry->day_id);
    }
}
