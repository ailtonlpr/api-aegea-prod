<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;

class TestController extends Controller
{

    public function index()
    {
        // protected $connection = "oracle";
        // $cars = DB::connection('oracle')->table('S_CARROS')->get();
        // die('humm...');
        $carros = DB::select('select * from S_CARROS');
        echo '<pre>';
        print_r($carros);
        die('humm');
    }
}