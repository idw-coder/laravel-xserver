<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\DB;;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return "Connected successfully to the database.";
    } catch (\Exception $e) {
        return "Could not connect to the database. " . $e->getMessage();
    }
});
