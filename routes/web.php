<?php
use Illuminate\Support\Facades\Route;
// Route::get('/test', 'Testcontroller@test');
Route::get('/testing', function() {
    return view('/tests/test');
});
Route::get('/form_elements', function() {
    return view('/tests/form_elements');
});
