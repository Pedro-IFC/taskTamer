<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Machine;

class MachineController extends Controller
{
    public function register(Request $request){
        $user = Auth::user();
        $user->machines()->create([
            'url' => $request->url,
            'senha' => $request->password,
            'name' => $request->name,
        ]);
        return redirect(route('dashboard'));
    }
    public function view(Request $request) {
        $maquina = Machine::where('id', $request->id)->first();
        return view('dashboard_machine', ["maquina" => $maquina]);
    }
    public function delete(Request $request) {
        $maquina = Machine::where('id', $request->id)->first();
        $maquina->delete();
        return redirect(route('dashboard'));
    }
}
