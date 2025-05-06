<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ConveniosController;
use App\Http\Controllers\Api\InstituicaoController;
use App\Http\Controllers\Api\SimulacaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/convenios', [ConveniosController::class, 'getConvenios']);

Route::get('/instituicoes', [InstituicaoController::class, 'getInstituicoes']);

Route::post('/simulacao', [SimulacaoController::class, 'calcSimulacao']);


