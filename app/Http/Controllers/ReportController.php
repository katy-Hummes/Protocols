<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportRequest;
use App\Models\Access;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function store(ReportRequest $request)
    {
        // dd($request->all());
        Report::create([
            'protocol_id' => $request->protocol_id,
            'status' => $request->status,
            'description' => $request->description,
        ]);
    }

     // acessos para departamentos
     public function access(Request $request, $id)
     {
         // Verifica se já existe uma entrada para este usuário neste departamento
         $existingAccess = Access::where('user_id', $request->user_id)
             ->where('department_id', $id)
             ->exists();
         // Se já existir, retorna para a mesma página com a mensagem de erro
         if ($existingAccess) {
             return redirect()->back();
         }
         // Caso contrário, cria o novo acesso
         Access::create([
             'user_id' => $request->user_id,
             'department_id' => $id,
         ]);
     }
 
     // Remover Acesso de Departamento
     public function destroy($id)
     {
         $access = Access::find($id);
         $access->delete();
     }
}
