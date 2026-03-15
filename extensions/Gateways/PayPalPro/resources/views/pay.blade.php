<div class="space-y-4">
    <div class="rounded-xl border border-neutral bg-background-secondary p-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold">PayPal Pro</h3>
                <p class="text-sm text-base/60">Use Apple Pay or Google Pay through PayPal.</p>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2 text-xs font-semibold">
                <span class="rounded-full border border-neutral px-3 py-1">Apple Pay</span>
                <span class="rounded-full border border-neutral px-3 py-1">Google Pay</span>
            </div>
        </div>
    </div>

    <div id="paypal-pro-wallets" class="space-y-4">
        <div id="paypal-pro-applepay" class="hidden">
            <div class="mb-2 text-sm font-medium">Apple Pay</div>
            <div id="applepay-container"></div>
        </div>

        <div id="paypal-pro-googlepay" class="hidden">
            <div class="mb-2 text-sm font-medium">Google Pay</div>
            <div id="googlepay-container"></div>
        </div>

        <div id="paypal-pro-message" class="hidden rounded-lg border border-neutral bg-background p-4 text-sm text-base/70">
            Apple Pay and Google Pay are not available on this device or PayPal account yet.
        </div>
    </div>
</div>

@script
    <script>
        (() => {
            const orderId = @js($order->id ?? null);
            const captureUrl = @js(route('extensions.gateways.paypal_pro.capture'));
            const currencyCode = @js($invoice->currency_code);
            const total = @js(number_format((float) $total, 2, '.', ''));
            const clientId = @js($clientId);
            const buyerCountry = @js($buyerCountry);
            const merchantId = @js($merchantId ?: null);
            const companyName = @js($companyName);
            const googleEnvironment = @js($isSandbox ? 'TEST' : 'PRODUCTION');

            const message = document.getElementById('paypal-pro-message');
            const applePaySection = document.getElementById('paypal-pro-applepay');
            const googlePaySection = document.getElementById('paypal-pro-googlepay');
            let hasVisibleWallet = false;

            const showFallbackMessage = () => {
                if (!hasVisibleWallet && message) {
                    message.classList.remove('hidden');
                }
            };

            const showWallet = (element) => {
                hasVisibleWallet = true;
                message?.classList.add('hidden');
                element?.classList.remove('hidden');
            };

            const loadScript = (src, attributes = {}) => new Promise((resolve, reject) => {
                if (document.querySelector(`script[src="${src}"]`)) {
                    resolve();
                    return;
                }

                const script = document.createElement('script');
                script.src = src;
                script.async = true;

                Object.entries(attributes).forEach(([key, value]) => {
                    script.setAttribute(key, value);
                });

                script.onload = resolve;
                script.onerror = reject;
                document.body.appendChild(script);
            });

            const captureOrder = async () => {
                const response = await fetch(`${captureUrl}?orderID=${encodeURIComponent(orderId)}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                const data = await response.json();
                const errorDetail = data?.details?.[0];

                if (!response.ok || errorDetail) {
                    throw new Error(errorDetail?.description || 'Unable to capture the PayPal order.');
                }

                window.location.href = @js(route('invoices.show', $invoice) . '?checkPayment=true');

                return data;
            };

            const buildPayPalSdkUrl = () => {
                const url = new URL('https://www.paypal.com/sdk/js');
                url.searchParams.set('client-id', clientId);
                url.searchParams.set('currency', currencyCode);
                url.searchParams.set('components', 'applepay,googlepay');
                url.searchParams.set('buyer-country', buyerCountry);

                if (merchantId) {
                    url.searchParams.set('merchant-id', merchantId);
                }

                return url.toString();
            };

            const initApplePay = async () => {
                if (!window.paypal?.Applepay || !window.ApplePaySession || !ApplePaySession.canMakePayments()) {
                    return;
                }

                const applepay = paypal.Applepay();
                const applepayConfig = await applepay.config();

                if (!applepayConfig?.isEligible) {
                    return;
                }

                showWallet(applePaySection);

                const container = document.getElementById('applepay-container');
                container.innerHTML = '<apple-pay-button buttonstyle="black" type="buy" locale="en"></apple-pay-button>';

                const button = container.querySelector('apple-pay-button');
                button.addEventListener('click', async () => {
                    const paymentRequest = {
                        countryCode: applepayConfig.countryCode,
                        merchantCapabilities: applepayConfig.merchantCapabilities,
                        supportedNetworks: applepayConfig.supportedNetworks,
                        currencyCode,
                        total: {
                            label: companyName,
                            type: 'final',
                            amount: total,
                        },
                    };

                    const session = new ApplePaySession(4, paymentRequest);

                    session.onvalidatemerchant = async (event) => {
                        try {
                            const merchantSession = await applepay.validateMerchant({
                                validationUrl: event.validationURL,
                                displayName: companyName,
                            });

                            session.completeMerchantValidation(merchantSession?.merchantSession ?? merchantSession);
                        } catch (error) {
                            console.error(error);
                            session.abort();
                        }
                    };

                    session.onpaymentauthorized = async (event) => {
                        try {
                            const result = await applepay.confirmOrder({
                                orderId,
                                token: event.payment.token,
                                billingContact: event.payment.billingContact,
                            });

                            if (!['APPROVED', 'COMPLETED'].includes(result?.status)) {
                                throw new Error('Apple Pay confirmation was not approved.');
                            }

                            await captureOrder();
                            session.completePayment(ApplePaySession.STATUS_SUCCESS);
                        } catch (error) {
                            console.error(error);
                            session.completePayment(ApplePaySession.STATUS_FAILURE);
                        }
                    };

                    session.begin();
                });
            };

            const initGooglePay = async () => {
                if (!window.paypal?.Googlepay || !window.google?.payments?.api) {
                    return;
                }

                const googlePay = paypal.Googlepay();
                const googlePayConfig = await googlePay.config();

                if (!googlePayConfig?.allowedPaymentMethods?.length) {
                    return;
                }

                const paymentsClient = new google.payments.api.PaymentsClient({
                    environment: googleEnvironment,
                    paymentDataCallbacks: {
                        onPaymentAuthorized: async (paymentData) => {
                            try {
                                const result = await googlePay.confirmOrder({
                                    orderId,
                                    paymentMethodData: paymentData.paymentMethodData,
                                    billingAddress: paymentData.paymentMethodData?.info?.billingAddress,
                                    email: paymentData.email,
                                });

                                if (!['APPROVED', 'COMPLETED'].includes(result?.status)) {
                                    return {
                                        transactionState: 'ERROR',
                                        error: {
                                            intent: 'PAYMENT_AUTHORIZATION',
                                            message: 'Google Pay confirmation was not approved.',
                                        },
                                    };
                                }

                                await captureOrder();

                                return {
                                    transactionState: 'SUCCESS',
                                };
                            } catch (error) {
                                console.error(error);

                                return {
                                    transactionState: 'ERROR',
                                    error: {
                                        intent: 'PAYMENT_AUTHORIZATION',
                                        message: error?.message || 'Google Pay failed.',
                                    },
                                };
                            }
                        },
                    },
                });

                const isReadyToPayRequest = {
                    apiVersion: 2,
                    apiVersionMinor: 0,
                    allowedPaymentMethods: googlePayConfig.allowedPaymentMethods,
                };

                const ready = await paymentsClient.isReadyToPay(isReadyToPayRequest);

                if (!ready?.result) {
                    return;
                }

                showWallet(googlePaySection);

                const button = paymentsClient.createButton({
                    onClick: async () => {
                        const paymentDataRequest = {
                            apiVersion: 2,
                            apiVersionMinor: 0,
                            allowedPaymentMethods: googlePayConfig.allowedPaymentMethods,
                            merchantInfo: googlePayConfig.merchantInfo,
                            transactionInfo: {
                                currencyCode,
                                totalPriceStatus: 'FINAL',
                                totalPrice: total,
                            },
                            callbackIntents: ['PAYMENT_AUTHORIZATION'],
                            emailRequired: true,
                        };

                        await paymentsClient.loadPaymentData(paymentDataRequest);
                    },
                    allowedPaymentMethods: googlePayConfig.allowedPaymentMethods,
                });

                document.getElementById('googlepay-container').appendChild(button);
            };

            Promise.all([
                loadScript(buildPayPalSdkUrl()),
                loadScript('https://applepay.cdn-apple.com/jsapi/1.latest/apple-pay-sdk.js'),
                loadScript('https://pay.google.com/gp/p/js/pay.js'),
            ]).then(async () => {
                if (!orderId || !window.paypal) {
                    showFallbackMessage();
                    return;
                }

                await Promise.allSettled([
                    initApplePay(),
                    initGooglePay(),
                ]);

                showFallbackMessage();
            }).catch((error) => {
                console.error(error);
                showFallbackMessage();
            });
        })();
    </script>
@endscript
