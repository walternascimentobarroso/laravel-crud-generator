<?php


Route::group(['namespace' => 'WalterNascimentoBarroso\CrudGenerator\Http\Controllers', 'middleware' => ['web']], function(){
    Route::get('testeproject', 'testeController@index');
});
