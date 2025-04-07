<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/nasabah/template', function () {
    $path = public_path('exports/nasabah_template.csv');
    return response()->download($path, 'nasabah_template.csv');
})->name('nasabah.template');
