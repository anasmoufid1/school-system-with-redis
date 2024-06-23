<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Filiere extends Model
{

    public static function getAllFilieres(){
        $filiereKeys = Redis::keys('filiere:*');
        $filieres = [];
        foreach ($filiereKeys as $filiereKey) {
            $filiereData = Redis::hgetall($filiereKey);
            $filieres[] = [
                'keys' => $filiereKey,
                'id' => $filiereData['identifier'],
                'label' => $filiereData['label'],
                'description' => $filiereData['description'],
                'prerequies' => $filiereData['prerequies'],
            ];
        }
        return $filieres;
    }

    public static function getFillieresModules($filiereId){
        $moduleKeys = Redis::keys("module:*");
        $modules = [];

    foreach ($moduleKeys as $moduleKey) {
        $moduleData = Redis::hgetall($moduleKey);
        if ($moduleData['filiere'] == $filiereId) {
            $modules[] = [
                'label' => $moduleData['label'],
                'description' => $moduleData['description'],
            ];
        }
    }

    return $modules;
    }
    public static function getFiliereName($filiereID){
        $filiere=Redis::hgetall($filiereID);
        return $filiere['label'];
    }



    





    public static function saveFiliere($request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'required|string',
            'prerequies' => 'required|string',
        ]);

        try {
            $filiereID = Redis::incr('filieres_counter');
            $filiereData = [
                'identifier' => $request->input('identifier', $filiereID),
                'label' => $request->input('label'),
                'description' => $request->input('description'),
                'prerequies' => $request->input('prerequies'),
            ];
            Redis::hmset("filiere:$filiereID", $filiereData);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteFiliere($filiereID)
    {
        Redis::del($filiereID);
        Redis::decr('filieres_counter');
    }

    public static function deleteAllFilieres()
    {
        $filieres=Redis::keys('filiere:*');
        foreach($filieres as $filiere){
            Redis::del($filiere);
        }
        Redis::del('filieres_counter');
    }
    public static function updateFiliere($id,$request){
        $filiere = Redis::hgetall($id);
        try {
            $filiereData = [
                'label' => $request->input('label'),
                'description' => $request->input('description'),
                'prerequies' => $request->input('prerequies'),
            ];
            Redis::hmset($id, $filiereData);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


}
