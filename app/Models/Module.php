<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use App\Models\Filiere;

class Module extends Model
{
    public static function getAllmodules(){
        $moduleKeys = Redis::keys('module:*');
        $modules = [];
        foreach ($moduleKeys as $moduleKey) {
            $moduleData = Redis::hgetall($moduleKey);
            $modules[] = [
                'keys' => $moduleKey,
                'id' => $moduleData['identifier'],
                'label' => $moduleData['label'],
                'description' => $moduleData['description'],
                'filiere' => Filiere::getFiliereName($moduleData['filiere']),
                'filiereid'=>$moduleData['filiere'],
            ];
        }
        return $modules;
    }

    public static function savemodule($request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'filiere' => 'required',
        ]);

        try {
            $moduleID = Redis::incr('modules_counter');
            $moduleData = [
                'identifier' => $request->input('identifier', $moduleID),
                'label' => $request->input('label'),
                'description' => $request->input('description'),
                'filiere' => $request->input('filiere'),
            ];
            Redis::hmset("module:$moduleID", $moduleData);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deletemodule($moduleID)
    {
        Redis::del($moduleID);
        Redis::decr('modules_counter');
    }

    public static function deleteAllmodules()
    {
        $modules=Redis::keys('module:*');
        foreach($modules as $module){
            Redis::del($module);
        }
        Redis::del('modules_counter');
    }
    public static function updatemodule($id,$request){
        $module = Redis::hgetall($id);
        try {
            $moduleData = [
                'label' => $request->input('label'),
                'description' => $request->input('description'),
                'filiere' => $request->input('filiere'),
            ];
            Redis::hmset($id, $moduleData);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
