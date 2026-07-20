<?php

use App\Http\Controllers\Api\BankWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/bank-transfer', [BankWebhookController::class, 'handleBankTransfer'])
    ->name('api.webhooks.bank_transfer');
