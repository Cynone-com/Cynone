@php($paypalProDomId = 'paypal-pro-' . ($order->id ?? 'checkout'))
@php($hasPreselectedWallet = filled($selectedWalletOption ?? null))

<div class="space-y-4">
    @unless($hasPreselectedWallet)
    <div class="rounded-xl border border-neutral bg-background-secondary p-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold">PayPal Pro</h3>
                <p class="text-sm text-base/60">Choose Apple Pay or Google Pay to open a wallet-styled checkout flow.</p>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2 text-xs font-semibold">
                <span class="rounded-full border border-neutral px-3 py-1">Apple Pay</span>
                <span class="rounded-full border border-neutral px-3 py-1">Google Pay</span>
            </div>
        </div>
    </div>
    @endunless

    <div id="{{ $paypalProDomId }}-selector" class="space-y-3 {{ $hasPreselectedWallet ? 'hidden' : '' }}">
        <div class="grid gap-3 md:grid-cols-2">
            <button type="button" data-wallet-option="apple" data-paypal-pro-root="{{ $paypalProDomId }}"
                class="flex items-center justify-between rounded-xl border border-neutral bg-background-secondary p-4 text-left transition hover:border-primary">
                <div>
                    <div class="text-base font-semibold">Apple Pay</div>
                    <div class="text-sm text-base/60">Open Apple Pay styled checkout</div>
                </div>
                <span class="rounded-full border border-neutral px-3 py-1 text-xs font-semibold">PayPal</span>
            </button>

            <button type="button" data-wallet-option="google" data-paypal-pro-root="{{ $paypalProDomId }}"
                class="flex items-center justify-between rounded-xl border border-neutral bg-background-secondary p-4 text-left transition hover:border-primary">
                <div>
                    <div class="text-base font-semibold">Google Pay</div>
                    <div class="text-sm text-base/60">Open Google Pay styled checkout</div>
                </div>
                <span class="rounded-full border border-neutral px-3 py-1 text-xs font-semibold">PayPal</span>
            </button>
        </div>
    </div>

    <div id="{{ $paypalProDomId }}-checkout" class="{{ $hasPreselectedWallet ? '' : 'hidden' }} rounded-xl border border-neutral bg-background-secondary p-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p id="{{ $paypalProDomId }}-selected-wallet" class="text-sm font-semibold text-primary"></p>
                <h4 id="{{ $paypalProDomId }}-checkout-title" class="text-lg font-semibold">Wallet Checkout</h4>
                <p id="{{ $paypalProDomId }}-checkout-subtitle" class="text-sm text-base/60">
                    Complete your wallet styled checkout below.
                </p>
            </div>
            <button type="button" id="{{ $paypalProDomId }}-change-method" class="text-sm font-medium text-primary">
                Change option
            </button>
        </div>

        <div id="{{ $paypalProDomId }}-apple-dummy" class="mt-5 hidden space-y-4">
            <div class="overflow-hidden rounded-[1.75rem] bg-[#1f1f22] text-white shadow-xl">
                <div class="px-6 pb-6 pt-5">
                    <div class="mb-5 flex items-center justify-between">
                        <div class="text-lg font-semibold">Apple Pay</div>
                        <div class="rounded-full bg-white/10 px-3 py-1 text-xs font-medium">Demo</div>
                    </div>

                    <div class="rounded-[1.5rem] bg-black px-5 py-4 shadow-inner">
                        <div class="flex items-center justify-between text-sm text-white/80">
                            <span>Default Card</span>
                            <span>•••• 4242</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-2xl font-semibold">Pay</div>
                            <div class="text-right text-xs text-white/70">
                                <div>Amount Due</div>
                                <div class="mt-1 text-base font-semibold text-white">{{ $invoice->formattedRemaining }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <div class="text-xs uppercase tracking-[0.2em] text-white/50">Billing Contact</div>
                            <div class="mt-2 text-sm font-medium">Omar Cynone</div>
                            <div class="mt-1 text-sm text-white/65">hostpanel.1987.ai</div>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <div class="text-xs uppercase tracking-[0.2em] text-white/50">Wallet Device</div>
                            <div class="mt-2 text-sm font-medium">iPhone Ready</div>
                            <div class="mt-1 text-sm text-white/65">Face ID confirmation simulation</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-neutral bg-background p-4">
                <div class="mb-2 text-sm font-semibold">Fallback Apple Pay card review</div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Cardholder</div>
                        <div class="mt-1 font-medium">Omar Cynone</div>
                    </div>
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Card</div>
                        <div class="mt-1 font-medium">Visa ending in 4242</div>
                    </div>
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Invoice</div>
                        <div class="mt-1 font-medium">#{{ $invoice->number ?? $invoice->id }}</div>
                    </div>
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Amount</div>
                        <div class="mt-1 font-medium">{{ $invoice->formattedRemaining }}</div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" id="{{ $paypalProDomId }}-apple-confirm"
                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-white px-4 py-3 text-sm font-semibold text-black transition hover:bg-white/90">
                    Confirm Apple Pay Demo
                </button>
            </div>
        </div>

        <div id="{{ $paypalProDomId }}-google-dummy" class="mt-5 hidden space-y-4">
            <div class="overflow-hidden rounded-[1.75rem] border border-black/5 bg-white text-[#202124] shadow-xl">
                <div class="px-6 pb-6 pt-5">
                    <div class="mb-5 flex items-center justify-between">
                        <div class="text-lg font-semibold">Google Pay</div>
                        <div class="rounded-full bg-[#f1f3f4] px-3 py-1 text-xs font-medium">Demo</div>
                    </div>

                    <div class="rounded-[1.5rem] bg-[#f8f9fa] px-5 py-4 shadow-inner">
                        <div class="flex items-center justify-between text-sm text-[#5f6368]">
                            <span>Saved Card</span>
                            <span>Mastercard •••• 4444</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <div class="text-[2rem] font-semibold tracking-tight">
                                    <span class="text-[#4285F4]">G</span><span class="text-[#EA4335]">o</span><span class="text-[#FBBC05]">o</span><span class="text-[#4285F4]">g</span><span class="text-[#34A853]">l</span><span class="text-[#EA4335]">e</span>
                                </div>
                                <div class="text-xl font-semibold">Pay</div>
                            </div>
                            <div class="text-right text-xs text-[#5f6368]">
                                <div>Amount Due</div>
                                <div class="mt-1 text-base font-semibold text-[#202124]">{{ $invoice->formattedRemaining }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl border border-black/10 bg-[#f8f9fa] p-4">
                            <div class="text-xs uppercase tracking-[0.2em] text-[#5f6368]">Google Account</div>
                            <div class="mt-2 text-sm font-medium">omar@cynone.test</div>
                            <div class="mt-1 text-sm text-[#5f6368]">Protected checkout simulation</div>
                        </div>
                        <div class="rounded-xl border border-black/10 bg-[#f8f9fa] p-4">
                            <div class="text-xs uppercase tracking-[0.2em] text-[#5f6368]">Device</div>
                            <div class="mt-2 text-sm font-medium">Chrome Wallet</div>
                            <div class="mt-1 text-sm text-[#5f6368]">Passkey-ready demo flow</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-neutral bg-background p-4">
                <div class="mb-2 text-sm font-semibold">Fallback Google Pay order review</div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Profile</div>
                        <div class="mt-1 font-medium">Primary Wallet Account</div>
                    </div>
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Card</div>
                        <div class="mt-1 font-medium">Mastercard ending in 4444</div>
                    </div>
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Invoice</div>
                        <div class="mt-1 font-medium">#{{ $invoice->number ?? $invoice->id }}</div>
                    </div>
                    <div class="rounded-lg border border-neutral bg-background-secondary p-3">
                        <div class="text-xs text-base/50">Amount</div>
                        <div class="mt-1 font-medium">{{ $invoice->formattedRemaining }}</div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" id="{{ $paypalProDomId }}-google-confirm"
                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-[#4285F4] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#357ae8]">
                    Confirm Google Pay Demo
                </button>
            </div>
        </div>

        <div id="{{ $paypalProDomId }}-dummy-message" class="mt-4 hidden rounded-lg border border-neutral bg-background p-4 text-sm text-base/70">
            This is a demo checkout section only. No live payment will be processed from PayPal Pro in this fallback mode.
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
                        Continue to Demo Checkout
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
                    Open the Google Pay styled demo checkout and review your purchase details before confirmation.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <button type="button" id="{{ $paypalProDomId }}-googlepay-continue"
                        class="rounded-xl bg-[#4285F4] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#357ae8]">
                        Continue to Demo Checkout
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
            const domId = @js($paypalProDomId);
            const preselectedWallet = @js($selectedWalletOption);

            const selector = document.getElementById(`${domId}-selector`);
            const checkout = document.getElementById(`${domId}-checkout`);
            const selectedWalletLabel = document.getElementById(`${domId}-selected-wallet`);
            const checkoutTitle = document.getElementById(`${domId}-checkout-title`);
            const checkoutSubtitle = document.getElementById(`${domId}-checkout-subtitle`);
            const changeMethodButton = document.getElementById(`${domId}-change-method`);
            const appleDummy = document.getElementById(`${domId}-apple-dummy`);
            const googleDummy = document.getElementById(`${domId}-google-dummy`);
            const dummyMessage = document.getElementById(`${domId}-dummy-message`);
            const applePayModal = document.getElementById(`${domId}-applepay-modal`);
            const applePayClose = document.getElementById(`${domId}-applepay-close`);
            const applePayCancel = document.getElementById(`${domId}-applepay-cancel`);
            const applePayContinue = document.getElementById(`${domId}-applepay-continue`);
            const googlePayModal = document.getElementById(`${domId}-googlepay-modal`);
            const googlePayClose = document.getElementById(`${domId}-googlepay-close`);
            const googlePayCancel = document.getElementById(`${domId}-googlepay-cancel`);
            const googlePayContinue = document.getElementById(`${domId}-googlepay-continue`);
            const appleConfirm = document.getElementById(`${domId}-apple-confirm`);
            const googleConfirm = document.getElementById(`${domId}-google-confirm`);

            if (!selector || !checkout || !selectedWalletLabel || !checkoutTitle || !checkoutSubtitle || !changeMethodButton || !appleDummy || !googleDummy || !dummyMessage || !applePayModal || !applePayClose || !applePayCancel || !applePayContinue || !googlePayModal || !googlePayClose || !googlePayCancel || !googlePayContinue || !appleConfirm || !googleConfirm) {
                return;
            }

            const closeModal = (modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            const openModal = (modal) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const resetCheckout = () => {
                appleDummy.classList.add('hidden');
                googleDummy.classList.add('hidden');
                dummyMessage.classList.add('hidden');
            };

            const openDummyCheckout = (wallet) => {
                selector.classList.add('hidden');
                checkout.classList.remove('hidden');
                resetCheckout();

                if (wallet === 'apple') {
                    selectedWalletLabel.textContent = 'Apple Pay selected';
                    checkoutTitle.textContent = 'Apple Pay Demo Checkout';
                    checkoutSubtitle.textContent = 'Review your Apple Pay styled fallback checkout below.';
                    appleDummy.classList.remove('hidden');
                } else {
                    selectedWalletLabel.textContent = 'Google Pay selected';
                    checkoutTitle.textContent = 'Google Pay Demo Checkout';
                    checkoutSubtitle.textContent = 'Review your Google Pay styled fallback checkout below.';
                    googleDummy.classList.remove('hidden');
                }

                dummyMessage.classList.remove('hidden');
            };

            document.addEventListener('click', (event) => {
                const button = event.target.closest(`[data-wallet-option][data-paypal-pro-root="${domId}"]`);

                if (!button) {
                    return;
                }

                event.preventDefault();

                if (button.dataset.walletOption === 'apple') {
                    openModal(applePayModal);
                } else {
                    openModal(googlePayModal);
                }
            });

            changeMethodButton.addEventListener('click', () => {
                checkout.classList.add('hidden');
                selector.classList.remove('hidden');
                resetCheckout();
            });

            applePayClose.addEventListener('click', () => closeModal(applePayModal));
            applePayCancel.addEventListener('click', () => closeModal(applePayModal));
            applePayContinue.addEventListener('click', () => {
                closeModal(applePayModal);
                openDummyCheckout('apple');
            });
            applePayModal.addEventListener('click', (event) => {
                if (event.target === applePayModal) {
                    closeModal(applePayModal);
                }
            });

            googlePayClose.addEventListener('click', () => closeModal(googlePayModal));
            googlePayCancel.addEventListener('click', () => closeModal(googlePayModal));
            googlePayContinue.addEventListener('click', () => {
                closeModal(googlePayModal);
                openDummyCheckout('google');
            });
            googlePayModal.addEventListener('click', (event) => {
                if (event.target === googlePayModal) {
                    closeModal(googlePayModal);
                }
            });

            appleConfirm.addEventListener('click', () => {
                dummyMessage.textContent = 'Apple Pay demo confirmed. This fallback does not process a live payment.';
                dummyMessage.classList.remove('hidden');
            });

            googleConfirm.addEventListener('click', () => {
                dummyMessage.textContent = 'Google Pay demo confirmed. This fallback does not process a live payment.';
                dummyMessage.classList.remove('hidden');
            });

            if (preselectedWallet === 'applepay') {
                openModal(applePayModal);
            } else if (preselectedWallet === 'googlepay') {
                openModal(googlePayModal);
            }
        })();
    </script>
@endscript
