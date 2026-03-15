<div class="space-y-4">
    <div class="rounded-xl border border-neutral bg-background-secondary p-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold">PayPal Pro</h3>
                <p class="text-sm text-base/60">Choose Apple Pay or Google Pay, then complete checkout with PayPal card processing.</p>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2 text-xs font-semibold">
                <span class="rounded-full border border-neutral px-3 py-1">Apple Pay</span>
                <span class="rounded-full border border-neutral px-3 py-1">Google Pay</span>
                <span class="rounded-full border border-neutral px-3 py-1">Cards</span>
            </div>
        </div>
    </div>

    <div id="paypal-pro-selector" class="space-y-3">
        <div class="grid gap-3 md:grid-cols-2">
            <button type="button" data-wallet-option="apple"
                class="paypal-pro-wallet-option flex items-center justify-between rounded-xl border border-neutral bg-background-secondary p-4 text-left transition hover:border-primary">
                <div>
                    <div class="text-base font-semibold">Apple Pay</div>
                    <div class="text-sm text-base/60">Continue with PayPal credit card checkout</div>
                </div>
                <span class="rounded-full border border-neutral px-3 py-1 text-xs font-semibold">PayPal</span>
            </button>

            <button type="button" data-wallet-option="google"
                class="paypal-pro-wallet-option flex items-center justify-between rounded-xl border border-neutral bg-background-secondary p-4 text-left transition hover:border-primary">
                <div>
                    <div class="text-base font-semibold">Google Pay</div>
                    <div class="text-sm text-base/60">Continue with PayPal credit card checkout</div>
                </div>
                <span class="rounded-full border border-neutral px-3 py-1 text-xs font-semibold">PayPal</span>
            </button>
        </div>
    </div>

    <div id="paypal-pro-card-checkout" class="hidden rounded-xl border border-neutral bg-background-secondary p-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p id="paypal-pro-selected-wallet" class="text-sm font-semibold text-primary"></p>
                <h4 class="text-lg font-semibold">PayPal Credit Card Checkout</h4>
                <p class="text-sm text-base/60">Enter the card details linked to your Apple Pay or Google Pay purchase flow.</p>
            </div>
            <button type="button" id="paypal-pro-change-method" class="text-sm font-medium text-primary">
                Change option
            </button>
        </div>

        <div id="paypal-pro-card-eligibility-message"
            class="mt-4 hidden rounded-lg border border-amber-500/30 bg-amber-500/10 p-3 text-sm text-amber-200">
            PayPal card fields are not eligible for this merchant account yet.
        </div>

        <div id="paypal-pro-card-form" class="mt-4 hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Name on card</label>
                    <div id="card-name-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Card number</label>
                    <div id="card-number-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Expiry</label>
                    <div id="card-expiry-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Security code</label>
                    <div id="card-cvv-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
            </div>

            <div id="paypal-pro-card-error"
                class="hidden rounded-lg border border-red-500/30 bg-red-500/10 p-3 text-sm text-red-200"></div>

            <button type="button" id="paypal-pro-card-submit"
                class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Pay {{ $invoice->formattedRemaining }}
            </button>
        </div>
    </div>
</div>

@script
    <script>
        (() => {
            window.__paymenterSdkLoads = window.__paymenterSdkLoads || {};

            const orderId = @js($order->id ?? null);
            const captureUrl = @js(route('extensions.gateways.paypal_pro.capture'));
            const currencyCode = @js($invoice->currency_code);
            const clientId = @js($clientId);

            const selector = document.getElementById('paypal-pro-selector');
            const cardCheckout = document.getElementById('paypal-pro-card-checkout');
            const selectedWalletLabel = document.getElementById('paypal-pro-selected-wallet');
            const changeMethodButton = document.getElementById('paypal-pro-change-method');
            const eligibilityMessage = document.getElementById('paypal-pro-card-eligibility-message');
            const cardForm = document.getElementById('paypal-pro-card-form');
            const submitButton = document.getElementById('paypal-pro-card-submit');
            const errorBox = document.getElementById('paypal-pro-card-error');

            let cardFieldsInstance = null;
            let cardFieldsRendered = false;

            const setError = (message) => {
                if (!message) {
                    errorBox.classList.add('hidden');
                    errorBox.textContent = '';
                    return;
                }

                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            };

            const loadScriptOnce = (key, src, selectorQuery = `script[src="${src}"]`) => {
                if (window.__paymenterSdkLoads[key]) {
                    return window.__paymenterSdkLoads[key];
                }

                window.__paymenterSdkLoads[key] = new Promise((resolve, reject) => {
                    const existing = document.querySelector(selectorQuery);
                    if (existing) {
                        if (existing.dataset.loaded === 'true') {
                            resolve();
                            return;
                        }

                        existing.addEventListener('load', () => resolve(), { once: true });
                        existing.addEventListener('error', reject, { once: true });
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = src;
                    script.async = true;
                    script.onload = () => {
                        script.dataset.loaded = 'true';
                        resolve();
                    };
                    script.onerror = reject;
                    document.body.appendChild(script);
                });

                return window.__paymenterSdkLoads[key];
            };

            const buildPayPalSdkUrl = () => {
                const url = new URL('https://www.paypal.com/sdk/js');
                url.searchParams.set('client-id', clientId);
                url.searchParams.set('currency', currencyCode);
                url.searchParams.set('components', 'card-fields');

                return url.toString();
            };

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

            const initCardFields = async () => {
                if (cardFieldsInstance) {
                    return cardFieldsInstance;
                }

                await loadScriptOnce(
                    'paypal-card-fields-sdk',
                    buildPayPalSdkUrl(),
                    'script[src^="https://www.paypal.com/sdk/js"]'
                );

                if (!window.paypal?.CardFields) {
                    throw new Error('PayPal card fields failed to load.');
                }

                cardFieldsInstance = paypal.CardFields({
                    createOrder: () => orderId,
                    onApprove: async () => {
                        await captureOrder();
                    },
                    onError: (error) => {
                        console.error(error);
                        setError(error?.message || 'Card payment failed. Please check your details and try again.');
                    },
                });

                return cardFieldsInstance;
            };

            const renderCardFields = async () => {
                setError('');

                const cardFields = await initCardFields();

                if (!cardFields.isEligible()) {
                    eligibilityMessage.classList.remove('hidden');
                    cardForm.classList.add('hidden');
                    return;
                }

                eligibilityMessage.classList.add('hidden');
                cardForm.classList.remove('hidden');

                if (cardFieldsRendered) {
                    return;
                }

                cardFieldsRendered = true;

                cardFields.NameField().render('#card-name-field-container');
                cardFields.NumberField().render('#card-number-field-container');
                cardFields.ExpiryField().render('#card-expiry-field-container');
                cardFields.CVVField().render('#card-cvv-field-container');

                submitButton.addEventListener('click', async () => {
                    setError('');
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-60', 'pointer-events-none');

                    try {
                        const state = await cardFields.getState();

                        if (!state?.isFormValid) {
                            throw new Error('Please complete all card fields before submitting.');
                        }

                        await cardFields.submit();
                    } catch (error) {
                        console.error(error);
                        setError(error?.message || 'Unable to submit card payment.');
                    } finally {
                        submitButton.disabled = false;
                        submitButton.classList.remove('opacity-60', 'pointer-events-none');
                    }
                }, { once: true });
            };

            const showCardCheckout = async (walletLabel) => {
                selectedWalletLabel.textContent = `${walletLabel} selected`;
                selector.classList.add('hidden');
                cardCheckout.classList.remove('hidden');

                try {
                    await renderCardFields();
                } catch (error) {
                    console.error(error);
                    setError(error?.message || 'Unable to load PayPal card checkout.');
                }
            };

            document.querySelectorAll('[data-wallet-option]').forEach((button) => {
                button.addEventListener('click', () => {
                    const wallet = button.dataset.walletOption === 'apple' ? 'Apple Pay' : 'Google Pay';
                    showCardCheckout(wallet);
                });
            });

            changeMethodButton?.addEventListener('click', () => {
                cardCheckout.classList.add('hidden');
                selector.classList.remove('hidden');
                setError('');
            });
        })();
    </script>
@endscript
