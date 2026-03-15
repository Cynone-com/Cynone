@php($paypalProDomId = 'paypal-pro-' . ($order->id ?? 'checkout'))
@php($hasPreselectedWallet = filled($selectedWalletOption ?? null))

<div class="space-y-4">
    @unless($hasPreselectedWallet)
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
    @endunless

    <div id="{{ $paypalProDomId }}-selector" class="space-y-3 {{ $hasPreselectedWallet ? 'hidden' : '' }}">
        <div class="grid gap-3 md:grid-cols-2">
            <button type="button" data-wallet-option="apple" data-paypal-pro-root="{{ $paypalProDomId }}"
                class="paypal-pro-wallet-option flex items-center justify-between rounded-xl border border-neutral bg-background-secondary p-4 text-left transition hover:border-primary">
                <div>
                    <div class="text-base font-semibold">Apple Pay</div>
                    <div class="text-sm text-base/60">Continue with PayPal credit card checkout</div>
                </div>
                <span class="rounded-full border border-neutral px-3 py-1 text-xs font-semibold">PayPal</span>
            </button>

            <button type="button" data-wallet-option="google" data-paypal-pro-root="{{ $paypalProDomId }}"
                class="paypal-pro-wallet-option flex items-center justify-between rounded-xl border border-neutral bg-background-secondary p-4 text-left transition hover:border-primary">
                <div>
                    <div class="text-base font-semibold">Google Pay</div>
                    <div class="text-sm text-base/60">Continue with PayPal credit card checkout</div>
                </div>
                <span class="rounded-full border border-neutral px-3 py-1 text-xs font-semibold">PayPal</span>
            </button>
        </div>
    </div>

    <div id="{{ $paypalProDomId }}-card-checkout" class="{{ $hasPreselectedWallet ? '' : 'hidden' }} rounded-xl border border-neutral bg-background-secondary p-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p id="{{ $paypalProDomId }}-selected-wallet" class="text-sm font-semibold text-primary"></p>
                <h4 class="text-lg font-semibold">PayPal Credit Card Checkout</h4>
                <p class="text-sm text-base/60">Enter the card details linked to your Apple Pay or Google Pay purchase flow.</p>
            </div>
            <button type="button" id="{{ $paypalProDomId }}-change-method" class="text-sm font-medium text-primary">
                Change option
            </button>
        </div>

        <div id="{{ $paypalProDomId }}-card-eligibility-message"
            class="mt-4 hidden rounded-lg border border-amber-500/30 bg-amber-500/10 p-3 text-sm text-amber-200">
            PayPal card fields are not eligible for this merchant account yet.
        </div>

        <div id="{{ $paypalProDomId }}-card-loading"
            class="mt-4 hidden rounded-lg border border-neutral bg-background p-3 text-sm text-base/70">
            Loading PayPal credit card checkout...
        </div>

        <div id="{{ $paypalProDomId }}-card-form" class="mt-4 hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium">Name on card</label>
                    <div id="{{ $paypalProDomId }}-card-name-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Card number</label>
                    <div id="{{ $paypalProDomId }}-card-number-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Expiry</label>
                    <div id="{{ $paypalProDomId }}-card-expiry-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Security code</label>
                    <div id="{{ $paypalProDomId }}-card-cvv-field-container"
                        class="rounded-lg border border-neutral bg-background px-3 py-3 min-h-[50px]"></div>
                </div>
            </div>

            <div id="{{ $paypalProDomId }}-card-error"
                class="hidden rounded-lg border border-red-500/30 bg-red-500/10 p-3 text-sm text-red-200"></div>

            <button type="button" id="{{ $paypalProDomId }}-card-submit"
                class="inline-flex w-full items-center justify-center rounded-lg bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90">
                Pay {{ $invoice->formattedRemaining }}
            </button>
        </div>

        <div id="{{ $paypalProDomId }}-fallback-paypal" class="mt-4 hidden space-y-3">
            <div class="rounded-lg border border-neutral bg-background p-3 text-sm text-base/70">
                Standard PayPal checkout is available as a fallback if card fields are unavailable.
            </div>
            <div id="{{ $paypalProDomId }}-button-container"></div>
        </div>
    </div>
</div>

@script
    <script>
        (() => {
            window.__paymenterSdkLoads = window.__paymenterSdkLoads || {};

            const domId = @js($paypalProDomId);
            const orderId = @js($order->id ?? null);
            const captureUrl = @js(route('extensions.gateways.paypal_pro.capture'));
            const currencyCode = @js($invoice->currency_code);
            const clientId = @js($clientId);
            const preselectedWallet = @js($selectedWalletOption);

            const selector = document.getElementById(`${domId}-selector`);
            const cardCheckout = document.getElementById(`${domId}-card-checkout`);
            const selectedWalletLabel = document.getElementById(`${domId}-selected-wallet`);
            const changeMethodButton = document.getElementById(`${domId}-change-method`);
            const eligibilityMessage = document.getElementById(`${domId}-card-eligibility-message`);
            const loadingBox = document.getElementById(`${domId}-card-loading`);
            const cardForm = document.getElementById(`${domId}-card-form`);
            const fallbackPayPal = document.getElementById(`${domId}-fallback-paypal`);
            const submitButton = document.getElementById(`${domId}-card-submit`);
            const errorBox = document.getElementById(`${domId}-card-error`);

            if (!selector || !cardCheckout || !selectedWalletLabel || !changeMethodButton || !eligibilityMessage || !loadingBox || !cardForm || !fallbackPayPal || !submitButton || !errorBox) {
                return;
            }

            let cardFieldsInstance = null;
            let cardFieldsRendered = false;
            let fallbackButtonsRendered = false;

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
                url.searchParams.set('components', 'buttons,card-fields');

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

            const renderFallbackButtons = async () => {
                if (fallbackButtonsRendered) {
                    fallbackPayPal.classList.remove('hidden');
                    return;
                }

                await loadScriptOnce(
                    'paypal-card-fields-sdk',
                    buildPayPalSdkUrl(),
                    'script[src^="https://www.paypal.com/sdk/js"]'
                );

                if (!window.paypal?.Buttons) {
                    return;
                }

                fallbackButtonsRendered = true;
                fallbackPayPal.classList.remove('hidden');

                paypal.Buttons({
                    style: {
                        shape: 'rect',
                        layout: 'vertical',
                        color: 'gold',
                        label: 'paypal',
                    },
                    createOrder: () => orderId,
                    onApprove: async () => {
                        await captureOrder();
                    },
                    onError: (error) => {
                        console.error(error);
                        setError(error?.message || 'Unable to load PayPal checkout.');
                    },
                }).render(`#${domId}-button-container`);
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
                loadingBox.classList.remove('hidden');
                fallbackPayPal.classList.add('hidden');

                const cardFields = await initCardFields();

                if (!cardFields.isEligible()) {
                    eligibilityMessage.classList.remove('hidden');
                    cardForm.classList.add('hidden');
                    loadingBox.classList.add('hidden');
                    await renderFallbackButtons();
                    return;
                }

                eligibilityMessage.classList.add('hidden');
                cardForm.classList.remove('hidden');
                loadingBox.classList.add('hidden');

                if (cardFieldsRendered) {
                    return;
                }

                cardFieldsRendered = true;

                cardFields.NameField().render(`#${domId}-card-name-field-container`);
                cardFields.NumberField().render(`#${domId}-card-number-field-container`);
                cardFields.ExpiryField().render(`#${domId}-card-expiry-field-container`);
                cardFields.CVVField().render(`#${domId}-card-cvv-field-container`);

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
                    loadingBox.classList.add('hidden');
                    fallbackPayPal.classList.remove('hidden');
                    setError(error?.message || 'Unable to load PayPal card checkout.');
                    await renderFallbackButtons();
                }
            };

            document.addEventListener('click', (event) => {
                const button = event.target.closest(`[data-wallet-option][data-paypal-pro-root="${domId}"]`);

                if (!button) {
                    return;
                }

                event.preventDefault();
                const wallet = button.dataset.walletOption === 'apple' ? 'Apple Pay' : 'Google Pay';
                if (button.dataset.loading === 'true') {
                    return;
                }
                button.dataset.loading = 'true';

                Promise.resolve(showCardCheckout(wallet)).finally(() => {
                    delete button.dataset.loading;
                });
            });

            changeMethodButton?.addEventListener('click', () => {
                cardCheckout.classList.add('hidden');
                selector.classList.remove('hidden');
                loadingBox.classList.add('hidden');
                fallbackPayPal.classList.add('hidden');
                setError('');
            });

            if (preselectedWallet === 'applepay') {
                showCardCheckout('Apple Pay');
            } else if (preselectedWallet === 'googlepay') {
                showCardCheckout('Google Pay');
            }
        })();
    </script>
@endscript
