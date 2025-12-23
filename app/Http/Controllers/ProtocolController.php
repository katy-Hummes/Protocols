<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProtocolRequest;
use App\Models\Access;
use App\Models\Department;
use App\Models\DocAttach;
use App\Models\People;
use App\Models\Protocol;
use Carbon\Carbon;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

// Criar protocolos
class ProtocolController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->active === 'N') {
            return redirect()->back();
        }

        // Definir departamentos com base no tipo
        $allowedDepartments = ($user->type === 'A')
            ? $user->departments->pluck('id')
            : Department::pluck('id');

        $departments = Department::whereIn('id', $allowedDepartments)->get(['id', 'name']);
        $peoples = People::get(['id', 'name']);
        $protocols = Protocol::with(['people', 'latestReport', 'department'])
            ->whereIn('department_id', $allowedDepartments)
            ->get();

        return Inertia::render('Protocols', [
            'protocols' => $protocols,
            'peoples' => $peoples,
            'departments' => $departments
        ]);
    }




    public function store(ProtocolRequest $request)
    {
        // dd($request->all());
        $selectedDate = Carbon::parse($request->date)->format('Y-m-d');
        $protocol = Protocol::create([
            'department_id' => $request->department_id,
            'people_id' => $request->people_id,
            'description' => $request->description,
            'date' => $selectedDate,
            'term' => $request->term
        ]);

        Report::create([
            'protocol_id' => $protocol->id,
        ]);

        if ($protocol) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public', $fileName);

                    DocAttach::create([
                        'protocol_id' => $protocol->id,
                        'file' => $fileName,
                    ]);
                }
            }
        }
    }

    // Editar protocolos
    public function show($id)
    {
        $user = Auth::user();

        if ($user->active === 'N') {
            return redirect()->back();
        }

        // Definir os departamentos com base no tipo
        $allowedDepartments = ($user->type === 'A')
            ? $user->departments->pluck('id')
            : Department::pluck('id');

        // Buscar o protocolo com restrições de departamentos
        $protocol = Protocol::with('people', 'docattachs')
            ->whereIn('department_id', $allowedDepartments)
            ->findOrFail($id);

        // Obter apenas os departamentos permitidos para o usuário
        $departments = Department::whereIn('id', $allowedDepartments)->get(['id', 'name']);
        $peoples = People::get(['id', 'name']);
        $reports = Report::where('protocol_id', $id)->get();

        return Inertia::render('EditProtocol', [
            'protocol' => $protocol,
            'departments' => $departments,
            'peoples' => $peoples,
            'reports' => $reports,
        ]);
    }


    public function deleteAttachment($id)
    {
        $attachment = DocAttach::find($id);
        if ($attachment) {
            Storage::delete('public/' . $attachment->file);

            $attachment->delete();
        }
    }
    public function update(ProtocolRequest $request, $id)
    {
        $selectedDate = Carbon::parse($request->date)->format('Y-m-d');
        $protocol = Protocol::find($id);
        $protocol->update([
            'department_id' => $request->department_id,
            'people_id' => $request->people_id,
            'description' => $request->description,
            'date' => $selectedDate,
            'term' => $request->term,
        ]);

        if ($protocol) {
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public', $fileName);

                    DocAttach::create([
                        'protocol_id' => $protocol->id,
                        'file' => $fileName,
                    ]);
                }
            }
        }
    }

    // Excluir protocolos
    public function destroy($id)
    {
        Protocol::destroy($id);
    }
}
