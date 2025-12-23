<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeopleRequest;
use App\Models\People;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PeopleController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();
        if ($authUser->active === 'N') {
            return redirect()->back();
        }
        $peoples = People::all();
        return Inertia::render('Peoples', [
            'peoples' => $peoples
        ]);
    }

    public function store(PeopleRequest $request)
    {
        // dd($request->all());
        $selectedDate = Carbon::parse($request->birth)->format('Y-m-d');
        People::create([
            'name' => $request->name,
            'birth' => $selectedDate,
            'cpf' => $request->birth,
            'sex' => $request->sex,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'street' => $request->street,
            'number' => $request->number,
            'complement' => $request->complement,
        ]);
    }

    public function show($id)
    {
        $authUser = Auth::user();
        if ($authUser->active === 'N') {
            return redirect()->back();
        }
        
        $people = People::find($id);
        // dd($people);
        return Inertia::render('EditPeople', [
            'people' => $people
        ]);
    }

    public function update(PeopleRequest $request, $id)
    {
        // dd($request->all());

        $selectedDate = Carbon::parse($request->birth)->format('Y-m-d');
        $cpfUnmasked = preg_replace('/[^0-9]/', '', $request->cpf);
        $people = People::find($id);
        $people->update([
            'name' => $request->name,
            'birth' => $selectedDate,
            'cpf' => $cpfUnmasked,
            'sex' => $request->sex,
            'city' => $request->city,
            'neighborhood' => $request->neighborhood,
            'street' => $request->street,
            'number' => $request->number,
            'complement' => $request->complement,
        ]);
    }
    public function destroy($id)
    {
        People::destroy($id);
    }
}
