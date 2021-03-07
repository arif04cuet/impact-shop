<?php

Route::get('/mmid/expertbox/mollie/handler/return/{request_id}', 'Mmid\Expertbox\Classes\Mollie@returnHandler')->middleware('web');

Route::post('/mmid/expertbox/mollie/handler/webhook', 'Mmid\Expertbox\Classes\Mollie@webhookHandler');

?>