<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redis;

use Illuminate\Http\Request;

class DashController extends Controller
{
    public function index(){
        $nbetudiants=Redis::get('etudiants_counter');
        $nbfilieres=Redis::get('filieres_counter');
        $nbmodules=Redis::get('modules_counter');
        return view('dash',compact('nbetudiants','nbfilieres','nbmodules'));
    }
}
