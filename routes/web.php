<?php
use Illuminate\Support\Facades\Route;
// Route::get('/test', 'Testcontroller@test');
Route::get('/testing', function() {
    return view('/tests/test');
});
