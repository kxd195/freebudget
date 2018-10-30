<?php

namespace App\Http\Controllers;

use App\Day;
use App\Scene;
use Illuminate\Http\Request;

class SceneController extends Controller {
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
		return view('scenes.list', ['list' => Scene::all()->sortBy('name')]);
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
		return SceneController::edit($id);
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$entry = $id !== 0 ? Scene::findOrFail($id) : new Scene();
		return view('scenes.edit', ['entry' => $entry]);
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $scene_id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, int $scene_id) {
		$this->run_validation($request);
		
		$appliesTo = $request->get('appliesTo');
		$budget_id = $request->get('budget_id');

		if ($appliesTo === 'day') {
			// applies to this day only
			$day_id = $request->get('day_id');

			$new_scene = new Scene();
			$new_scene->fill($request->all());
			$new_scene->save();
			$new_scene_id = $new_scene->id;

			foreach (Day::findOrFail($day_id)->people as $person) {
				if ($person->scene_id === $scene_id) {
					$person->scene_id = $new_scene_id;
                    $person->disableVersioning();
					$person->save();
                    $person->enableVersioning();
				}
			}

		} else {
			// applies to all days
			$scene = Scene::findOrFail($scene_id)->fill($request->all());
			$scene->save();
		}

        Scene::removeOrphans($budget_id);

		return redirect()->route('budgets.show', $budget_id)
				->with('message', 'Your changes has been successfully saved!');
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		if (!Scene::destroy($id)) {
			return redirect()->back()
			->with('message-danger', 'Something wrong happened while deleting')
			->withInput();
		}
		
		return redirect()->route('scenes.index')
				->with('message-warning', 'Scene has been successfully deleted!');
	}
}
