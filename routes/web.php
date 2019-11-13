<?php

// Route::get('/test', 'Testcontroller@test');
Route::get('/test', function() {
    return view('/tests/test');
});