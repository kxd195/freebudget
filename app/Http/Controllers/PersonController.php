<?php

namespace App\Http\Controllers;

use App\Budget;
use App\Category;
use App\Day;
use App\Person;
use App\RateClass;
use App\Scene;
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
			/*
			'qty' => 'required|numeric|min:1',
			'description' => 'required',
			'rate_class_id' => 'required',
			'hours' => 'required|numeric',
			*/
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
	public function create(Request $request) {
		return $this->edit($request, 0);
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
	public function edit(Request $request, $id) {
		$entry = $id !== 0 ? Person::findOrFail($id) : new Person();

		if ($id === 0) {
			$entry->budget_id = $request->budget_id;
			$entry->budget = Budget::find($entry->budget_id);
		}
		
		if (request('day_id') !== null)
			$entry->day_id = request('day_id');

		if ($id === 0)
			$entry->scene = session('last_scene');
	   
		$categories = Category::with('rate_classes')->get()->sortBy(function($i) {
			return strpos(strtoupper($i->name), 'ADDONS') === 0 ? 'ZZZ' . $i->name : $i->name;
		});

		$day_entries = $entry->budget->days->sortBy(['is_default_day DESC', 'actualdate']);
		$day_entries->map(function($i) {
			// add the day name and actualdate to a new column called display_name
			$i['display_name'] = $i->generateName();
			return $i;
		});

		$days = $day_entries->pluck('display_name', 'id');

		$units = Unit::all()->pluck('name', 'id');
		
		// if we're making a copy, make sure we clear the id so it doesn't overwrite it
		if (request('copy') !== null)
			$entry->id = null;

		$entry->modifiable_people = collect([$entry]);

		if (request('whole_scene')) {
			$entry->modifiable_people = collect();
			$people = $entry->day !== null ? $entry->day->people : $entry->budget->undated_entries;
			foreach ($people as $person) {
				if ($person->scene_id === $entry->scene_id) {
					if (request('copy') !== null)
						$person->id = null;

					$entry->modifiable_people->push($person);
				}
			}

		}

		return view('people.edit', ['entry' => $entry, 'days' => $days, 'categories' => $categories, 'units' => $units]);
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
		
		$day_ids = explode(',', $request->get('day_id'));

		$specified_people = [];

		if ($request->get('people') !== null)
			foreach ($request->get('people') as $person_data) {
				$person = isset($person_data['id']) && $person_data['id'] !== 0 ? Person::findOrFail($person_data['id'])->fill($request->all()) : new Person($request->all());

				if (isset($person_data['id']) && $person_data['id'] !== 0)
					$specified_people[] = $person_data['id'];

				if ($request->get('scene_option') === 'new') {
					$new_scene = new Scene();
					$new_scene->fill($request->get('scene'));
					$new_scene->budget_id = $person->budget_id;
					$new_scene->disableVersioning();
					$new_scene->save();
					$new_scene->enableVersioning();
					$person->scene_id = $new_scene->id;
				} else if ($request->get('scene_option') === 'none')
					$person->scene_id  = null;
				session(['last_scene' => $person->scene]);

				// fill with the rest of the data
				$person->fill($person_data);

				if (isset($person->description))
					$last_description = $person->description;
				else if (isset($last_description))
					$person->description = $last_description;
				else 
					continue;

				$isFirstEntry = true;
				foreach ($day_ids as $day_id) {
					$person_entry = $isFirstEntry ? $person : $person->replicate();
					$person_entry->day_id = empty($day_id) ? null : $day_id;

					$rateclass = RateClass::find($person_entry->rate_class_id);

					if ($rateclass != null) {
						if ($rateclass->code === 'NUT' && $person_entry->cost_overridden
								&& $person_entry->cost > $rateclass->rate) {
							$person_entry->rate_class_id = RateClass::where('code', 'NUHR')->first()->id;
						}

						if ($rateclass->code === 'AS')
							$person_entry->scene_id = null;

						if ($rateclass->is_daily)
							$person_entry->hours = 1;
					} else {
						$person_entry->hours = 0;
					}

					$person_entry->disableVersioning();
					$person_entry->save();
					$isFirstEntry = false;
				}
			}

		// delete anyone that was deleted
		if ($request->get('original_people') !== null) {
			foreach ($request->get('original_people') as $original_people => $unused) {
				if (!in_array($original_people, $specified_people)) {
					$person = Person::find($original_people);

					if ($person !== null)
						$person->delete();
				}

			}
		}

        Scene::removeOrphans($request->budget_id);

		return redirect()->to(route('budgets.show', $request->budget_id) . '#day-' . $day_ids[0])
				->with('message', 'Your changes has been successfully saved!');
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
		
		return redirect()->route('budgets.show', $entry->budget_id)
				->with('message-warning', 'The person "' . $entry->description . '" has been successfully deleted!')
				->with('show_day_id', $entry->day_id);
	}

	public function delete(Request $request) {
		if (isset($request->delete_entry_id))
			foreach ($request->delete_entry_id as $id)
				Person::findOrFail($id)->delete();
		
		return redirect()->route('budgets.show', $request->budget_id)
				->with('message-warning', 'The selected entries has been successfully deleted!');
	}
}
