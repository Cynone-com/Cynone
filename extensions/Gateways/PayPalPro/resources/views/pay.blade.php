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

        <div id="{{ $paypalProDomId }}-dummy-checkout" class="mt-4 hidden space-y-4">
            <div class="rounded-lg border border-dashed border-neutral bg-background p-4">
                <div class="mb-3 text-sm font-semibold">Fallback card form</div>
                <p class="mb-4 text-sm text-base/60">
                    The live PayPal card fields did not load, so this backup checkout layout is being shown instead.
                </p>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Cardholder name</label>
                        <input type="text" disabled placeholder="John Doe"
                            class="w-full rounded-lg border border-neutral bg-background-secondary px-3 py-3 text-sm opacity-80" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Card number</label>
                        <input type="text" disabled placeholder="4111 1111 1111 1111"
                            class="w-full rounded-lg border border-neutral bg-background-secondary px-3 py-3 text-sm opacity-80" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Expiry date</label>
                        <input type="text" disabled placeholder="12/30"
                            class="w-full rounded-lg border border-neutral bg-background-secondary px-3 py-3 text-sm opacity-80" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Security code</label>
                        <input type="text" disabled placeholder="123"
                            class="w-full rounded-lg border border-neutral bg-background-secondary px-3 py-3 text-sm opacity-80" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="{{ $paypalProDomId }}-applepay-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/55 p-4">
        <div class="relative w-full max-w-[580px] overflow-hidden rounded-[2rem] bg-[#232326] text-white shadow-2xl">
            <button type="button" id="{{ $paypalProDomId }}-applepay-close"
                class="absolute right-5 top-5 flex size-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20">
                <span class="text-2xl leading-none">&times;</span>
            </button>

            <div class="flex flex-col items-center px-8 pb-8 pt-10">
                <div class="relative mb-8 flex size-[250px] items-center justify-center rounded-full">
                    <div class="absolute inset-0 rounded-full border-[6px] border-dashed border-white/70"></div>
                    <div class="absolute inset-[18px] rounded-full border-[6px] border-dashed border-white/45"></div>
                    <div class="absolute inset-[36px] rounded-full border-[6px] border-dashed border-white/80"></div>
                    <div class="absolute inset-[54px] rounded-full border-[6px] border-dashed border-white/35"></div>
                    <div class="absolute inset-[72px] rounded-full border-[6px] border-dashed border-white/70"></div>
                    <div class="absolute inset-[90px] flex items-center justify-center rounded-full bg-white text-black shadow-lg">
                        <div class="flex items-center gap-1 text-3xl font-semibold tracking-tight">
                            <span class="text-2xl"></span>
                            <span>Pay</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-black/20 px-8 pb-6 pt-7 text-center">
                <h3 class="text-[2rem] font-semibold leading-tight">Scan Code with iPhone</h3>
                <p class="mx-auto mt-3 max-w-md text-lg leading-8 text-white/85">
                    Use the Camera app to continue your Apple Pay purchase on your iPhone. Requires iOS 18 or later.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <button type="button" id="{{ $paypalProDomId }}-applepay-continue"
                        class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-black transition hover:bg-white/90">
                        Continue to Checkout
                    </button>
                    <button type="button" id="{{ $paypalProDomId }}-applepay-cancel"
                        class="rounded-xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Cancel
                    </button>
                </div>

                <p class="mt-7 text-xs text-white/60">
                    Copyright &copy; 2026 Apple Inc. All rights reserved. <span class="underline">Privacy Policy</span>
                </p>
            </div>
        </div>
    </div>

    <div id="{{ $paypalProDomId }}-googlepay-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/55 p-4">
        <div class="relative w-full max-w-[580px] overflow-hidden rounded-[2rem] bg-white text-[#202124] shadow-2xl">
            <button type="button" id="{{ $paypalProDomId }}-googlepay-close"
                class="absolute right-5 top-5 flex size-10 items-center justify-center rounded-full bg-black/5 text-[#202124] transition hover:bg-black/10">
                <span class="text-2xl leading-none">&times;</span>
            </button>

            <div class="px-8 pb-8 pt-10 text-center">
                <div class="mx-auto mb-8 flex size-[250px] items-center justify-center rounded-full bg-[#f1f3f4]">
                    <div class="text-center">
                        <div class="text-[2.8rem] font-semibold tracking-tight">
                            <span class="text-[#4285F4]">G</span><span class="text-[#EA4335]">o</span><span class="text-[#FBBC05]">o</span><span class="text-[#4285F4]">g</span><span class="text-[#34A853]">l</span><span class="text-[#EA4335]">e</span>
                        </div>
                        <div class="mt-1 text-3xl font-semibold">Pay</div>
                    </div>
                </div>

                <h3 class="text-[2rem] font-semibold leading-tight">Continue with Google Pay</h3>
                <p class="mx-auto mt-3 max-w-md text-lg leading-8 text-[#5f6368]">
                    Your Google Pay path is ready. If the live checkout does not load, a dummy card checkout fallback will appear automatically.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <button type="button" id="{{ $paypalProDomId }}-googlepay-continue"
                        class="rounded-xl bg-[#4285F4] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#357ae8]">
                        Continue to Checkout
                    </button>
                    <button type="button" id="{{ $paypalProDomId }}-googlepay-cancel"
                        class="rounded-xl border border-black/10 bg-white px-5 py-3 text-sm font-semibold text-[#202124] transition hover:bg-[#f8f9fa]">
                        Cancel
                    </button>
                </div>
            </div>
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
            const dummyCheckout = document.getElementById(`${domId}-dummy-checkout`);
            const submitButton = document.getElementById(`${domId}-card-submit`);
            const errorBox = document.getElementById(`${domId}-card-error`);
            const applePayModal = document.getElementById(`${domId}-applepay-modal`);
            const applePayClose = document.getElementById(`${domId}-applepay-close`);
            const applePayCancel = document.getElementById(`${domId}-applepay-cancel`);
            const applePayContinue = document.getElementById(`${domId}-applepay-continue`);
            const googlePayModal = document.getElementById(`${domId}-googlepay-modal`);
            const googlePayClose = document.getElementById(`${domId}-googlepay-close`);
            const googlePayCancel = document.getElementById(`${domId}-googlepay-cancel`);
            const googlePayContinue = document.getElementById(`${domId}-googlepay-continue`);

            if (!selector || !cardCheckout || !selectedWalletLabel || !changeMethodButton || !eligibilityMessage || !loadingBox || !cardForm || !fallbackPayPal || !dummyCheckout || !submitButton || !errorBox || !applePayModal || !applePayClose || !applePayCancel || !applePayContinue || !googlePayModal || !googlePayClose || !googlePayCancel || !googlePayContinue) {
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
                    dummyCheckout.classList.remove('hidden');
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
                dummyCheckout.classList.remove('hidden');

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
                dummyCheckout.classList.add('hidden');

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

            const closeApplePayModal = () => {
                applePayModal.classList.add('hidden');
                applePayModal.classList.remove('flex');
            };

            const openApplePayModal = () => {
                applePayModal.classList.remove('hidden');
                applePayModal.classList.add('flex');
            };

            const closeGooglePayModal = () => {
                googlePayModal.classList.add('hidden');
                googlePayModal.classList.remove('flex');
            };

            const openGooglePayModal = () => {
                googlePayModal.classList.remove('hidden');
                googlePayModal.classList.add('flex');
            };

            const showCardCheckout = async (walletLabel) => {
                selectedWalletLabel.textContent = `${walletLabel} selected`;
                selector.classList.add('hidden');
                cardCheckout.classList.remove('hidden');
                dummyCheckout.classList.add('hidden');

                try {
                    await renderCardFields();
                } catch (error) {
                    console.error(error);
                    loadingBox.classList.add('hidden');
                    fallbackPayPal.classList.remove('hidden');
                    dummyCheckout.classList.remove('hidden');
                    setError(error?.message || 'Unable to load PayPal card checkout.');
                    await renderFallbackButtons();
                }
            };

            const handleWalletSelection = async (walletLabel) => {
                if (walletLabel === 'Apple Pay') {
                    openApplePayModal();
                    return;
                }

                if (walletLabel === 'Google Pay') {
                    openGooglePayModal();
                    return;
                }

                await showCardCheckout(walletLabel);
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

                Promise.resolve(handleWalletSelection(wallet)).finally(() => {
                    delete button.dataset.loading;
                });
            });

            changeMethodButton?.addEventListener('click', () => {
                cardCheckout.classList.add('hidden');
                selector.classList.remove('hidden');
                loadingBox.classList.add('hidden');
                fallbackPayPal.classList.add('hidden');
                dummyCheckout.classList.add('hidden');
                setError('');
            });

            if (preselectedWallet === 'applepay') {
                openApplePayModal();
            } else if (preselectedWallet === 'googlepay') {
                openGooglePayModal();
            }

            applePayClose.addEventListener('click', closeApplePayModal);
            applePayCancel.addEventListener('click', () => {
                closeApplePayModal();
                cardCheckout.classList.add('hidden');
                selector.classList.remove('hidden');
            });
            applePayContinue.addEventListener('click', async () => {
                closeApplePayModal();
                await showCardCheckout('Apple Pay');
            });
            applePayModal.addEventListener('click', (event) => {
                if (event.target === applePayModal) {
                    closeApplePayModal();
                }
            });

            googlePayClose.addEventListener('click', closeGooglePayModal);
            googlePayCancel.addEventListener('click', () => {
                closeGooglePayModal();
                cardCheckout.classList.add('hidden');
                selector.classList.remove('hidden');
            });
            googlePayContinue.addEventListener('click', async () => {
                closeGooglePayModal();
                await showCardCheckout('Google Pay');
            });
            googlePayModal.addEventListener('click', (event) => {
                if (event.target === googlePayModal) {
                    closeGooglePayModal();
                }
            });
        })();
    </script>
@endscript
