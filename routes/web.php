<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PeopleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use App\Http\Controllers\ProtocolController;
use App\Http\Controllers\ReportController;
use App\Models\User;
use Illuminate\Routing\RouteGroup;
use OwenIt\Auditing\Contracts\Audit;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        // 'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Pessoas:
    Route::get('/pessoas', [PeopleController::class, 'index'])->name('people.index');
    Route::post('/pessoa', [PeopleController::class, 'store'])->name('people.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/editar-pessoa/{id}', [PeopleController::class, 'show'])->name('people.show');
    Route::put('/editar-pessoa/{id}', [PeopleController::class, 'update'])->name('people.update')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('/deletar-pessoa/{id}', [PeopleController::class, 'destroy'])->name('people.destroy')->middleware([HandlePrecognitiveRequests::class]);

    // Protocolos:
    Route::get('/protocolos', [ProtocolController::class, 'index'])->name('protocols.index');
    Route::post('/protocolo', [ProtocolController::class, 'store'])->name('protocol.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/editar-protocolo/{id}', [ProtocolController::class, 'show'])->name('protocol.show');
    Route::put('/editar-protocolo/{id}', [ProtocolController::class, 'update'])->name('protocol.update')->middleware([HandlePrecognitiveRequests::class]);
    Route::delete('/deletar-protocolo/{id}', [ProtocolController::class, 'destroy'])->name('protocol.destroy');
    Route::delete('/api/docattachs/{id}', [ProtocolController::class, 'deleteAttachment']);

    // Autenticação:
    Route::get('/registro', [AuthController::class, 'create'])->name('create');
    Route::post('usuario-registro', [AuthController::class, 'register'])->name('user.register')->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/usuarios', [AuthController::class, 'index'])->name('users.index');
    Route::get('/usuario/{id}', [AuthController::class, 'show'])->name('user.show');
    Route::post('/editar-usuario/{id?}', [AuthController::class, 'update'])->name('user.update')->middleware([HandlePrecognitiveRequests::class]);

    // Departamentos:
    Route::get('/departamentos', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departamento', [DepartmentController::class, 'store'])->name('department.store')->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/departamento/{id}', [DepartmentController::class, 'show'])->name('department.show');
    Route::put('/departamento/{id}', [DepartmentController::class, 'update'])->name('department.update')->middleware([HandlePrecognitiveRequests::class]);

    // Acessos aos departamentos:
    Route::post('/access/{id}', [ReportController::class, 'access'])->name('access')->middleware([HandlePrecognitiveRequests::class]);
    Route::post('access-destroy/{id}', [ReportController::class, 'destroy'])->name('access.destroy');

    // Acompanhamento:
    Route::post('/acompanhamento', [ReportController::class, 'store'])->name('store.Report')->middleware([HandlePrecognitiveRequests::class]);

    // Auditoria:
    Route::get('/auditoria', [AuditController::class, 'index'])->name('audit.index');
    Route::get('/auditoria/{id}', [AuditController::class, 'show'])->name('audit.show');


    Route::post('/api/validate-cpf', [PeopleController::class, 'validateCpf']);
});
