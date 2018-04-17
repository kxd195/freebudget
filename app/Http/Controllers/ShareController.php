<?php

namespace App\Http\Controllers;

use App\Share;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller {
    private $HASH_SALT = "ABCDEFGHI";
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $entry = new Share($request->all());
        $entry->user_id = Auth::id();
        
        $expires_after = intval($request->input('expires_after'));
        if ($expires_after !== 0) {
            $entry->expires_at = Carbon::now()->addDays($expires_after);
        }

        $entry->save();

        return response()->json([
            'hyperlink' => route('shares.external', $entry->toHash()),
            'modifiable' => $entry->modifiable,
            'expires_at' => $entry->expires_at != null ? $entry->expires_at->setTimezone('Canada/Pacific')->toDayDateTimeString() : null,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $entry = Share::findFromHash($id);
        
        if (isset($entry->expires_at) && Carbon::now()->gt($entry->expires_at))
            return redirect()->route('home')
                    ->with('message-danger', 'The shared hyperlink you accessed is no longer valid and has since expired. If you have login credentials, you can login now to access the application. If you do not have login credentials, please ask us for a new share hyperlink.');
            
        $budget_controller = new BudgetController();
        
        if (isset($entry->budget_version_id))
            return $budget_controller->showVersion($entry->budget_id, $entry->budget_version_id, true);
        else
            return $budget_controller->show($entry->budget_id, true, !$entry->modifiable);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
