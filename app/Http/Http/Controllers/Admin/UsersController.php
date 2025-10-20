<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Project;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->moduleName = 'User Management';

        view()->share('moduleName', $this->moduleName);
    }
    /**
     * Display a listing of User.
     *
     * //   * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!Gate::allows('user_access')) {
            return abort(403);
        }

        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param \App\Http\Requests\StoreUsersRequest $request
     * @return \Illuminate\Http\Response
     */
//   public function store(StoreUsersRequest $request)
// {
//     if (!Gate::allows('user_create')) {
//         return view('errors.403');
//     }

//     DB::beginTransaction();

//     try {
//         $password = $request->input('password');

//         $user = User::create($request->except('team_id'));
//         $user->teams()->sync($request->input('team_id'));

//         if ($request->has('permissions')) {
//             $user->permissions()->sync($request->input('permissions'));
//         }

//         $employeeData = [
//             'user_id' => $user->id,
//             'email' => $user->email,
//             'first_name' => $request->input('first_name') ?? "",
//             'last_name' => $request->input('last_name') ?? "",
//             'phone_number' => $request->input('phone_number') ?? "",
//             'bio' => $request->input('bio') ?? "",
//             'position' => $request->input('position') ?? "general",
//             'employee_type' => $request->input('employee_type') ?? "regular",
//             'start_date' => $request->input('start_date') ?? now(),
//             'end_date' => $request->input('end_date') ?? null,
//             'emergency_contact_name' => $request->input('emergency_contact_name'),
//             'emergency_contact_phone' => $request->input('emergency_contact_phone'),
//             'date_of_birth' => $request->input('date_of_birth'),
//             'daily_rate' => $request->input('daily_rate') ?? 0,
//             'overtime_rate' => $request->input('overtime_rate') ?? 0,
//             'bank_name' => $request->input('bank_name') ?? "N/A",
//             'bank_account_number' => $request->input('bank_account_number') ?? null,
//             'tax_number' => $request->input('tax_number') ?? null,
//             'leave_days_allowed' => $request->input('leave_days_allowed') ?? 10,
//             'leave_days_taken' => 0,
//             'sick_days_allowed' => $request->input('sick_days_allowed') ?? 5,
//             'sick_days_taken' => 0,
//             'days_absent' => 0,
//             'days_present' => 0,
//             'picture_path' => $request->hasFile('picture') ? $this->uploadPicture($request) : null,
//         ];

//         $existingEmployee = Employee::where('email', $user->email)->first();

//         if ($existingEmployee) {
//             return redirect()->route('admin.users.create')->with('error', 'Employee With Same Email Already Exists!');
// //            dd($existingEmployee);
//             // $existingEmployee->update($employeeData);
//         } else {
//             //  dd("Into thelse condition");
//             // Employee::create($employeeData);
//         }

//         try {
//             $user->notify(new WelcomeNotification($password));
//             Log::channel('notifications')->info(__class__ . ' ' . __function__ . ': Welcome notification sent successfully', [
//                 'user_id' => $user->id,
//                 'user_email' => $user->email
//             ]);
//         } catch (\Exception $e) {
//             Log::error(__class__ . ' ' . __function__ . ': Failed to send welcome notification', [
//                 'user_id' => $user->id,
//                 'user_email' => $user->email,
//                 'error' => $e->getMessage()
//             ]);
//             Log::channel('notifications')->error(__class__ . ' ' . __function__ . ': Failed to send welcome notification', [
//                 'user_id' => $user->id,
//                 'user_email' => $user->email,
//                 'error' => $e->getMessage()
//             ]);
//         }

//         DB::commit();
//         return redirect()->route('admin.users.create')->with('success', 'Record created or updated successfully.');
//     } catch (\Exception $e) {
//         DB::rollBack();
//         Log::error(__class__ . ' ' . __function__ . ': Failed to create or update user and employee', [
//             'error' => $e->getMessage()
//         ]);
//         return redirect()->back()->with('error', 'Failed to process user and employee: ' . $e->getMessage());
//     }
// }

    public function store(StoreUsersRequest $request)
    {
        if (!Gate::allows('user_create')) {
            return view('errors.403');
        }

        DB::beginTransaction();

        try {
            $password = $request->input('password');

            // Create the user
            $user = User::create($request->except('team_id'));
            $user->teams()->sync($request->input('team_id'));

            if ($request->has('permissions')) {
                $user->permissions()->sync($request->input('permissions'));
            }

            // Prepare employee data
            $employeeData = [
                'user_id' => $user->id,
                'email' => $user->email,
                'first_name' => $request->input('first_name') ?? "",
                'last_name' => $request->input('last_name') ?? "",
                'phone_number' => $request->input('phone_number') ?? "",
                'bio' => $request->input('bio') ?? "",
                'position' => $request->input('position') ?? "general",
                'employee_type' => $request->input('employee_type') ?? "regular",
                'start_date' => $request->input('start_date') ?? now(),
                'end_date' => $request->input('end_date') ?? null,
                'emergency_contact_name' => $request->input('emergency_contact_name'),
                'emergency_contact_phone' => $request->input('emergency_contact_phone'),
                'date_of_birth' => $request->input('date_of_birth'),
                'daily_rate' => $request->input('daily_rate') ?? 0,
                'overtime_rate' => $request->input('overtime_rate') ?? 0,
                'bank_name' => $request->input('bank_name') ?? "N/A",
                'bank_account_number' => $request->input('bank_account_number') ?? null,
                'tax_number' => $request->input('tax_number') ?? null,
                'leave_days_allowed' => $request->input('leave_days_allowed') ?? 10,
                'leave_days_taken' => 0,
                'sick_days_allowed' => $request->input('sick_days_allowed') ?? 5,
                'sick_days_taken' => 0,
                'days_absent' => 0,
                'days_present' => 0,
                'picture_path' => $request->hasFile('picture') ? $this->uploadPicture($request) : null,
            ];

            // Check if employee exists
            $existingEmployee = Employee::where('email', $user->email)->first();

            if ($existingEmployee) {
                // Update existing employee
                $existingEmployee->update($employeeData);
            } else {
                // Create new employee
                Employee::create($employeeData);
            }

            // Send welcome notification
            try {
                $user->notify(new WelcomeNotification($password));
                Log::channel('notifications')->info(__class__ . ' ' . __function__ . ': Welcome notification sent successfully', [
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            } catch (\Exception $e) {
                Log::error(__class__ . ' ' . __function__ . ': Failed to send welcome notification', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                Log::channel('notifications')->error(__class__ . ' ' . __function__ . ': Failed to send welcome notification', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();
            return redirect()->route('admin.users.create')->with('success', 'User and Employee record created or updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__class__ . ' ' . __function__ . ': Failed to create or update user and employee', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to process user and employee: ' . $e->getMessage());
        }
    }




    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Gate::allows('user_create')) {
            return abort(401);
        }
        $roles = \App\Models\Role::get()->pluck('title', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $teams = \App\Models\Team::get()->pluck('name', 'id');
        $permissions = Permission::pluck('label', 'id');

        return view('admin.users.create', compact('roles', 'teams', 'permissions'));
    }

    /**
     * Show the form for editing User.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
//        echo "<pre>";
//        print_r($id);die;
        if (!Gate::allows('user_edit')) {
            return abort(401);
        }

        $roles = \App\Models\Role::pluck('title', 'id')->prepend(trans('quickadmin.qa_please_select'), '');
        $teams = \App\Models\Team::pluck('name', 'id');
        $permissions = Permission::pluck('label', 'id');

//        $user = User::with(['teams', 'permissions'])->findOrFail($id);
        $user = User::where('id',$id)->with(['teams', 'permissions'])->first();
        $employee = Employee::where('user_id', $id)->first();
        // return $employee;

        if ($employee) {
            foreach ($employee->toArray() as $key => $value) {
                $user->$key = $value;
            }
        }

        return view('admin.users.edit', compact('id','user', 'roles', 'teams', 'permissions','employee'));
    }


    /**
     * Update the specified User in storage.
     *
     * @param UpdateUsersRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
//         echo "<pre>";
//         print_r($request->all());die;
        if (!Gate::allows('user_edit')) {
            return abort(401);
        }

//        DB::beginTransaction();
        try {

            $user = User::where('id',$request->input('user_id'))->first();
             $user->update($request->except('team_id'));
            $user->teams()->sync($request->input('team_id'));

//             $user = User::findOrFail($id);
//             if ($request->input('email') === $user->email) {
//                 $request->request->remove('email');
//             }
//             $user->update($request->except('team_id'));
//             $user->teams()->sync($request->input('team_id'));



            if ($request->has('permissions')) {
                $user->permissions()->sync($request->input('permissions', []));
            } else {
                $user->permissions()->detach();
            }



            $employeeData = [
                'first_name' =>$request->input('name'),
                'last_name' =>$request->input('name'),
                'email' => $user->email,
                'phone_number' => $request->input('phone_number'),
                'position' => $request->input('position'),
                'employee_type' => $request->input('employee_type', 'regular'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'emergency_contact_name' => $request->input('emergency_contact_name'),
                'emergency_contact_phone' => $request->input('emergency_contact_phone'),
                'date_of_birth' => $request->input('date_of_birth'),
                'daily_rate' => $request->input('daily_rate'),
                'overtime_rate' => $request->input('overtime_rate'),
                'bank_name' => $request->input('bank_name'),
                'bank_account_number' => $request->input('bank_account_number'),
                'tax_number' => $request->input('tax_number'),
                'leave_days_allowed' => $request->input('leave_days_allowed'),
                'sick_days_allowed' => $request->input('sick_days_allowed', '0'),
                'bio' => $request->input('bio', 'N/A'),
                'picture_path' => $request->hasFile('picture')
                    ? $this->uploadPicture($request)
                    : null
            ];


            try {
                $emp = Employee::where('user_id', $request->input('user_id'))->update($employeeData);
            }catch (\Exception $e){
                return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
            }

            DB::commit();
            return redirect()->route('admin.users.index')->with('success', 'User and Employee updated successfully');
        } catch (\Exception $e) {
            // DB::rollBack();
            Log::error(__class__ . ' ' . __function__ . ': Failed to update user and employee', [
                'user_id' => $request->input('user_id'),
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Display User.
     *
     * @param int $id
     */
    public function show($id)
    {
        if (!Gate::allows('user_view')) {
            return abort(401);
        }
        $products = \App\Models\Product::where('created_by_id', $id)->get();
        $user = User::findOrFail($id);
        $userProjects = Project::where('team_leader_user_id', $user->id)->get();

        return view('admin.users.show', compact(
            'user',
            'userProjects'
        ));
    }

    /**
     * Remove User from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Gate::allows('user_delete')) {
            return abort(401);
        }
        $user = User::findOrFail($id);

        $emp = Employee::where('email',$user->email)->delete();
        User::where('id',$user->id)->delete();
        //        echo "<pre>";print_r(Employee::all());
        //        die;
        session()->flash('danger', 'User and related employee deleted successfully.');
        return redirect()->route('admin.users.index');
    }

//public function update(UpdateUsersRequest $request, $id)
//{
//    if (!Gate::allows('user_edit')) {
//        return abort(401);
//    }
//
//    DB::beginTransaction();
//
//    try {
//        // Find the user
//        $user = User::findOrFail($id);
//
//        // ✅ Skip check if the email is same as current
//        if ($request->input('email') !== $user->email) {
//            // ✅ Check only for other users
//            $emailExists = User::where('email', $request->input('email'))
//                ->where('id', '!=', $id)
//                ->exists();
//
//            if ($emailExists) {
//                return redirect()->back()->with('error', 'This email is already in use by another user.');
//            }
//        }
//
//        // ✅ Update user data (excluding teams)
//        $user->update($request->except('team_id'));
//
//        // ✅ Sync teams
//        $user->teams()->sync($request->input('team_id', []));
//
//        // ✅ Sync permissions
//        if ($request->has('permissions')) {
//            $user->permissions()->sync($request->input('permissions', []));
//        } else {
//            $user->permissions()->detach();
//        }
//
//        // ✅ Prepare employee data
//        $employeeData = [
//            'user_id' => $user->id,
//            'email' => $user->email,
//            'first_name' => $request->input('first_name', ''),
//            'last_name' => $request->input('last_name', ''),
//            'phone_number' => $request->input('phone_number', ''),
//            'position' => $request->input('position', 'general'),
//            'employee_type' => $request->input('employee_type', 'regular'),
//            'start_date' => $request->input('start_date', now()),
//            'end_date' => $request->input('end_date'),
//            'emergency_contact_name' => $request->input('emergency_contact_name'),
//            'emergency_contact_phone' => $request->input('emergency_contact_phone'),
//            'date_of_birth' => $request->input('date_of_birth'),
//            'daily_rate' => $request->input('daily_rate', 0),
//            'overtime_rate' => $request->input('overtime_rate', 0),
//            'bank_name' => $request->input('bank_name', 'N/A'),
//            'bank_account_number' => $request->input('bank_account_number'),
//            'tax_number' => $request->input('tax_number'),
//            'leave_days_allowed' => $request->input('leave_days_allowed', 10),
//            'sick_days_allowed' => $request->input('sick_days_allowed', 5),
//            'bio' => $request->input('bio', ''),
//            'picture_path' => $request->hasFile('picture') ? $this->uploadPicture($request) : null,
//        ];
//
//        // ✅ Update or create employee record
//        Employee::updateOrCreate(
//            ['user_id' => $user->id],
//            array_filter($employeeData)
//        );
//
//        DB::commit();
//
//        return redirect()->route('admin.users.index')->with('success', 'User and Employee updated successfully.');
//    } catch (\Exception $e) {
//        DB::rollBack();
//        Log::error(__CLASS__ . '::' . __FUNCTION__ . ' - Failed to update user', [
//            'user_id' => $id,
//            'error' => $e->getMessage()
//        ]);
//
//        return redirect()->back()->with('error', 'Failed to update user: ' . $e->getMessage());
//    }
//}



    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        if (!Gate::allows('user_delete')) {
            return abort(401);
        }
        if ($request->input('ids')) {
            $entries = User::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
        return redirect()->route('admin.users.index');
    }

    /**
     * Upload employee picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    private function uploadPicture(Request $request)
    {
        if ($request->hasFile('picture')) {
            $path = $request->file('picture')->store('employee_pictures', 'public');
            return str_replace('public/', '', $path);
        }
        return null;
    }
}
