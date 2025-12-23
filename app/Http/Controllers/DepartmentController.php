<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    // criar departamento
    public function index()
    {
        $authUser = Auth::user();
        if ($authUser->active === 'N') {
            return redirect()->back();
        }

        $userType = auth()->user()->type;
        if ($userType === 'A') {
            return redirect()->back();
        }
        return Inertia::render('Departments', [
            'departments' => Department::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Department::create([
            'name' => $request->name,
        ]);
    }
    // editar departamentos
    public function show($id)
    {
        $authUser = Auth::user();
        if ($authUser->active === 'N') {
            return redirect()->back();
        }
        
        $userType = auth()->user()->type;
        if ($userType === 'A') {
            return redirect()->back();
        }
        $accesses = Access::with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->where('department_id', $id)->get();

        $department = Department::findOrFail($id);
        return Inertia::render('EditDepartment', [
            'users' => User::get(['id', 'name']),
            'department' => $department,
            'accesses' => $accesses
        ]);
    }

    public function update(Request $request, $id)
    {

        // dd($request->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $department = Department::findOrFail($id);
        $department->update([
            'name' => $request->name,
        ]);
    }

}
