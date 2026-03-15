<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayPalPro\PayPalPro;

Route::post('/extensions/paypal-pro/capture', [PayPalPro::class, 'capture'])->name('extensions.gateways.paypal_pro.capture');
