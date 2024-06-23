<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Etudiant;
use App\Models\Filiere;
use App\Models\Module;
class EtudiantController extends Controller
{
public function index()
{
    $etudiants = Etudiant::getAllEtudiants();
    $filieres = Filiere::getAllFilieres();
    return view('etudiant.index', compact('etudiants','filieres'));
}
public function store(Request $request)
{
    $result = Etudiant::saveEtudiant($request);

    if ($result) {
        return redirect()->route('etudiant.index')->with('success', 'Étudiant ajouté avec succès.');
    } else {
        return redirect()->route('etudiant.index')->with('danger', 'Une erreur s\'est produite.');
    }
}

public function update($id,Request $request)
{
    Etudiant::updateEtudiant($id,$request);
    return redirect()->route('etudiant.index')->with('success', 'Étudiant modifié avec succès.');   
}

public function destroy($etudiantId)
{
    Etudiant::deleteEtudiant($etudiantId);
    return redirect()->route('etudiant.index');
}
public function deleteAll()
{
    Etudiant::deleteAllEtudiants();
    return redirect()->route('etudiant.index')->with('success', 'les insertion suprimées avec success');;
}
}
