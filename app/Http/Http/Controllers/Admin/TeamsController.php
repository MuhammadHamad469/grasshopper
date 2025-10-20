<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamsRequest;
use App\Http\Requests\Admin\UpdateTeamsRequest;
use App\Models\Product;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamsController extends Controller
{
    public function __construct()
    {
        $this->moduleName = 'User Management';

        view()->share('moduleName', $this->moduleName);
    }
    /**
     * Display a listing of Team.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! Gate::allows('team_access')) {
            return abort(403);
        }
        $teams = Team::all();

        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating new Team.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('team_create')) {
            return abort(401);
        }

        $users = User::pluck('name', 'id')->toArray();

        return view('admin.teams.create', compact('users'));
    }

    /**
     * Store a newly created Team in storage.
     *
     * @param  \App\Http\Requests\StoreTeamsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeamsRequest $request)
    {
        if (! Gate::allows('team_create')) {
            return abort(401);
        }
        // Talha code start
        $validatedData = $request->validate([
            'name' => 'required|unique:teams,name',
        ]);
        // Talha code end


        $team = Team::create($request->all());

        return redirect()->route('admin.teams.index');
    }


    /**
     * Show the form for editing Team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! Gate::allows('team_edit')) {
            return abort(401);
        }
        $team = Team::findOrFail($id);
        $users = User::pluck('name', 'id')->toArray();
        //				dd($users);

        return view('admin.teams.edit', compact('team', 'users'));
    }

    /**
     * Update Team in storage.
     *
     * @param  \App\Http\Requests\UpdateTeamsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeamsRequest $request, $id)
    {
        if (! Gate::allows('team_edit')) {
            return abort(401);
        }
        $team = Team::findOrFail($id);
        $team->update($request->all());

        return redirect()->route('admin.teams.index');
    }


    /**
     * Display Team.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (! Gate::allows('team_view')) {
            return abort(401);
        }
        $team = Team::findOrFail($id);
        $products = Product::where('created_by_team_id', $id)->get();
        $users = User::whereHas('teams', function ($q) use ($id) {
            $q->where('id', $id);
        })->get();

        return view('admin.teams.show', compact('team', 'products', 'users'));
    }


    /**
     * Remove Team from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! Gate::allows('team_delete')) {
            return abort(401);
        }
        $team = Team::findOrFail($id);
        $team->delete();
        session()->flash('success', 'Team deleted successfully.');

        return redirect()->route('admin.teams.index');
    }

    /**
     * Delete all selected Team at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (! Gate::allows('team_delete')) {
            return abort(401);
        }



        if ($request->input('ids')) {
            $entries = Team::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }
}
