<?php

use Lyra\Http\Response;
use Lyra\Routing\Route;

Route::get('/', fn ($request) => Response::text("Lyra"));
Route::get('/form', fn ($request) => view("form"));
