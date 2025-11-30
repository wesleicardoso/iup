<?php

use App\Http\Controllers\CnpjApiController;
use Illuminate\Support\Facades\Route;

Route::get('/api/cnpj/{cnpj}', [CnpjApiController::class, 'lookup'])->name('api.cnpj.lookup');


?>