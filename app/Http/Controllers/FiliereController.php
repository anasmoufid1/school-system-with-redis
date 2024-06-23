<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filiere;

class FiliereController extends Controller
{
    public function getAllFilieres(){
        $filiere = Filiere::getAllFilieres();
        return view('filiere.index', compact('filiere'));
    }
    public function index(){
        $filieres = Filiere::getAllFilieres();
        return view('filiere.index', compact('filieres'));
    }
    public function store(Request $request)
    {
        $result = Filiere::saveFiliere($request);

        if ($result) {
            return redirect()->route('filiere.index')->with('success', 'Filière ajoutée avec succès.');
        } else {
            return redirect()->route('filiere.index')->with('danger', 'Une erreur s\'est produite.');
        }
    }

    public function update($id,Request $request)
    {
        Filiere::updateFiliere($id,$request);
        return redirect()->route('filiere.index')->with('success', 'Filière modifié avec succès.');   
    }

    public function destroy($filiereid)
    {
        Filiere::deleteFiliere($filiereid);
        return redirect()->route('filiere.index');
    }
    public function deleteAll()
    {
        Filiere::deleteAllFilieres();
        return redirect()->route('filiere.index')->with('success', 'les insertion suprimées avec success');;
    }
}
