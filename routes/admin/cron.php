<?php

use Illuminate\Support\Facades\Route;

Route::get('/cron/update_agents', 'Cron\CronController@update_tables_agents');
Route::get('/cron/update_others', 'Cron\CronController@update_tables_other');

Route::get('/cron/get_emailed_docs', 'Cron\CronController@get_emailed_docs');
