<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
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

        $audits = Audit::with('user')->get();
        return Inertia::render('Audit/Index', [
            'audits' => $audits
        ]);
    }   

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
        $audit = Audit::with('user')->find($id);
        return Inertia::render('Audit/Show', [
            'audit' => $audit
        ]);
    }
}
