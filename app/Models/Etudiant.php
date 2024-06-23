<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use App\Models\Filiere;
class Etudiant extends Model
{
    public static function getAllEtudiants()
    {
        $etudiantKeys = Redis::keys('etudiant:*');
        $etudiants = [];
        foreach ($etudiantKeys as $etudiantKey) {
            $etudiantData = Redis::hgetall($etudiantKey);
            $etudiants[] = [
                'keys' => $etudiantKey,
                'id' => $etudiantData['identifier'],
                'name' => $etudiantData['name'],
                'lastname' => $etudiantData['lastname'],
                'birthdate' => $etudiantData['birthdate'],
                'filiere'=> Filiere::getFiliereName($etudiantData['filiere']),
                'modules'=> Filiere::getFillieresModules($etudiantData['filiere']),
                'image' => $etudiantData['image'],
            ];
        }
        return $etudiants;
    }
    public static function saveEtudiant($request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today',
        ]);

        try {
            $etudiantId = Redis::incr('etudiants_counter');
            $originalName = $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('images', $originalName, 'public');
            $etudiantData = [
                'identifier' => $request->input('identifier', $etudiantId),
                'name' => $request->input('name'),
                'lastname' => $request->input('lastname'),
                'birthdate' => $request->input('birthdate'),
                'filiere'=> $request->input('filiere'),
                'image' => $path,
            ];
            Redis::hmset("etudiant:$etudiantId", $etudiantData);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteEtudiant($etudiantId)
    {
        Redis::del($etudiantId);
        Redis::decr('etudiants_counter');
    }

    public static function deleteAllEtudiants()
    {
        $etudiants=Redis::keys('etudiant:*');
        foreach($etudiants as $etudiant){
            Redis::del($etudiant);
        }
        Redis::del('etudiants_counter');
    }
    public static function updateEtudiant($id,$request){
        $etudiant = Redis::hgetall('etudiant:'.$id);
        $defaultImagePath = $etudiant['image'];
        if ($request->hasFile('image')) {
            $originalName = $request->file('image')->getClientOriginalName();
            $defaultImagePath = $request->file('image')->storeAs('images', $originalName, 'public');
        }
        try {
            $etudiantData = [
                'name' => $request->input('name'),
                'lastname' => $request->input('lastname'),
                'birthdate' => $request->input('birthdate'),
                'filiere'=> $request->input('filiere'),
                'image' => $defaultImagePath,
            ];
            Redis::hmset("etudiant:$id", $etudiantData);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
