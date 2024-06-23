<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filiere;
use App\Models\Module;

class ModuleController extends Controller
{
    public function index(){
        $filieres = Filiere::getAllFilieres();
        $modules = Module::getAllModules();
        return view('module.index', compact('modules','filieres'));
    }
    public function store(Request $request)
    {
        $result = Module::savemodule($request);
    
        if ($result) {
            return redirect()->route('module.index')->with('success', 'Module ajouté avec succès.');
        } else {
            return redirect()->route('module.index')->with('danger', 'Une erreur s\'est produite.');
        }
    }
    
    public function update($id,Request $request)
    {
        module::updatemodule($id,$request);
        return redirect()->route('module.index')->with('success', 'Module modifié avec succès.');   
    }
    
    public function destroy($moduleId)
    {
        module::deletemodule($moduleId);
        return redirect()->route('module.index');
    }
    public function deleteAll()
    {
        module::deleteAllmodules();
        return redirect()->route('module.index')->with('success', 'les insertion suprimées avec success');;
    }
}
