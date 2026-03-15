<?php

namespace Paymenter\Extensions\Gateways\PayPalPro;

use App\Attributes\ExtensionMeta;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Paymenter\Extensions\Gateways\PayPal\PayPal;

#[ExtensionMeta(
    name: 'PayPal Pro Gateway',
    description: 'Accept Apple Pay and Google Pay through the PayPal API.',
    version: '1.0.0',
    author: 'Paymenter',
    url: 'https://developer.paypal.com/docs/checkout/apm/google-pay/',
    icon: 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxyZWN0IHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiByeD0iOTYiIGZpbGw9IiMxMTE4MjciLz4KICAgIDxyZWN0IHg9IjQ4IiB5PSIxMjAiIHdpZHRoPSIyMDAiIGhlaWdodD0iMTI4IiByeD0iMjQiIGZpbGw9IndoaXRlIi8+CiAgICA8cGF0aCBkPSJNMzE0IDE1Mmg5MmMyNi41MSAwIDQ4IDIxLjQ5IDQ4IDQ4czIxLjQ5IDQ4IDQ4IDQ4aC05MmMtMjYuNTEgMC00OC0yMS40OS00OC00OHMyMS40OS00OCA0OC00OFoiIGZpbGw9IiMwMDc0RjAiLz4KICAgIDxyZWN0IHg9IjQ4IiB5PSIyODgiIHdpZHRoPSI0MTYiIGhlaWdodD0iMTEyIiByeD0iNTYiIGZpbGw9IiMyQTJEQjAiLz4KICAgIDxwYXRoIGQ9Ik0xMDkgMzYwLjVoLTIwLjRsMjguMi00OC44aDIwLjRsLTI4LjIgNDguOFptNTcuNCAwSDE0Nmw5LjktMTYuOGgtMzAuNGwtOS43IDE2LjhIMDk1LjVsMjguMi00OC44aDAuMWgyMC4yaDUuMWg1LjJsLTI4LjEgNDguOFptODgtNDguOGgtMTkuN2wtMjEuMSAzNi4zbC0xMS0zNi4zaC0yMC43bDE2LjIgNDguOGgyMC41bDM1LjgtNDguOHptNzAuMSAyNC40YzAgMTQuNi0xMC42IDI0LjQtMjYuNCAyNC40aC0yNy4ybDI4LjItNDguOGgyNC4xYzE1LjUgMCAyMS4zIDguNiAyMS4zIDI0LjRabS0yMC42LjFjMC03LjEtMi45LTEwLjUtOS44LTEwLjVoLTcuNmwtMTQuNiAyNS4yaDcuNWM5LjkgMCAyNC41LTMuNiAyNC41LTE0LjdabTg5LjkgMjQuM2gtMTkuOGwtNC4xLTEwLjJIMzQ3bC04LjMgMTAuMmgtMjEuNGw0MS4xLTQ4LjhoMTguM2wxOSAyOC4yIDE2LjQtMjguMmgxOS44bC0yOC4xIDQ4Ljh6bS0zMS4xLTI0LjloLTEuM2wtMTEuMSAxMy42aMTcuNWwtNS4xLTEzLjZabTg0LjEgMjQuOWgtMjAuNGwyOC4yLTQ4LjhoMjAuNGwtMjguMiA0OC44WiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+Cg=='
)]
class PayPalPro extends PayPal
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        View::addNamespace('gateways.paypal-pro', __DIR__ . '/resources/views');
    }

    public function supportsBillingAgreements(): bool
    {
        return false;
    }

    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'client_id',
                'label' => 'Client ID',
                'type' => 'text',
                'description' => 'Find your API keys at https://developer.paypal.com/developer/applications',
                'required' => true,
            ],
            [
                'name' => 'client_secret',
                'label' => 'Client Secret',
                'type' => 'text',
                'description' => 'Find your API keys at https://developer.paypal.com/developer/applications',
                'required' => true,
            ],
            [
                'name' => 'merchant_id',
                'label' => 'Merchant ID',
                'type' => 'text',
                'description' => 'Required by PayPal for Apple Pay and Google Pay SDK integrations.',
                'required' => false,
            ],
            [
                'name' => 'buyer_country',
                'label' => 'Buyer Country',
                'type' => 'text',
                'description' => 'Two-letter country code used for wallet eligibility checks, for example US.',
                'required' => false,
            ],
            [
                'name' => 'test_mode',
                'label' => 'Test Mode',
                'type' => 'checkbox',
                'description' => 'Enable test mode',
                'required' => false,
            ],
        ];
    }

    public function pay($invoice, $total)
    {
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        $order = $this->request('post', $url . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'invoice_id' => $invoice->id,
                    'amount' => [
                        'currency_code' => $invoice->currency_code,
                        'value' => $total,
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => route('invoices.show', $invoice),
                'cancel_url' => route('invoices.show', $invoice),
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ]);

        return view('gateways.paypal-pro::pay', [
            'invoice' => $invoice,
            'total' => $total,
            'order' => $order ?? null,
            'clientId' => $this->config('client_id'),
            'merchantId' => $this->config('merchant_id'),
            'buyerCountry' => strtoupper($this->config('buyer_country') ?: 'US'),
            'companyName' => config('settings.company_name', config('app.name')),
            'isSandbox' => (bool) $this->config('test_mode'),
        ]);
    }

    public function capture(Request $request)
    {
        if (!$request->has('orderID')) {
            abort(400);
        }

        $orderID = $request->input('orderID');
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        $order = $this->request('get', $url . '/v2/checkout/orders/' . $orderID);
        if (($order->status ?? null) === 'COMPLETED') {
            return $order;
        }

        $response = $this->request('post', $url . '/v2/checkout/orders/' . $orderID . '/capture', [
            'intent' => 'CAPTURE',
        ]);

        $capture = $response->purchase_units[0]->payments->captures[0] ?? null;

        ExtensionHelper::addPayment(
            $order->purchase_units[0]->invoice_id,
            'PayPalPro',
            $capture?->amount?->value,
            $capture?->seller_receivable_breakdown?->paypal_fee?->value,
            $capture?->id
        );

        return $response;
    }
}
