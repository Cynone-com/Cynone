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

        <div id="{{ $paypalProDomId }}-wallet-error" class="mt-5 hidden rounded-xl border border-amber-500/30 bg-amber-500/10 p-5">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 rounded-full bg-amber-500/20 p-2 text-amber-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.29 3.86l-7.2 12.47A2 2 0 004.8 19.5h14.4a2 2 0 001.73-3.03l-7.2-12.47a2 2 0 00-3.46 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div id="{{ $paypalProDomId }}-wallet-error-title" class="text-base font-semibold text-amber-100">
                        Live payment required
                    </div>
                    <p id="{{ $paypalProDomId }}-wallet-error-message" class="mt-2 text-sm leading-6 text-amber-50/85">
                        Enable live payment to activate this wallet option.
                    </p>
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
            const walletError = document.getElementById(`${domId}-wallet-error`);
            const walletErrorTitle = document.getElementById(`${domId}-wallet-error-title`);
            const walletErrorMessage = document.getElementById(`${domId}-wallet-error-message`);
            const applePayModal = document.getElementById(`${domId}-applepay-modal`);
            const applePayClose = document.getElementById(`${domId}-applepay-close`);
            const applePayCancel = document.getElementById(`${domId}-applepay-cancel`);
            const applePayContinue = document.getElementById(`${domId}-applepay-continue`);
            const googlePayModal = document.getElementById(`${domId}-googlepay-modal`);
            const googlePayClose = document.getElementById(`${domId}-googlepay-close`);
            const googlePayCancel = document.getElementById(`${domId}-googlepay-cancel`);
            const googlePayContinue = document.getElementById(`${domId}-googlepay-continue`);

            if (!selector || !checkout || !selectedWalletLabel || !checkoutTitle || !checkoutSubtitle || !changeMethodButton || !walletError || !walletErrorTitle || !walletErrorMessage || !applePayModal || !applePayClose || !applePayCancel || !applePayContinue || !googlePayModal || !googlePayClose || !googlePayCancel || !googlePayContinue) {
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

            const openWalletError = (wallet) => {
                selector.classList.add('hidden');
                checkout.classList.remove('hidden');
                walletError.classList.remove('hidden');

                if (wallet === 'apple') {
                    selectedWalletLabel.textContent = 'Apple Pay selected';
                    checkoutTitle.textContent = 'Apple Pay unavailable';
                    checkoutSubtitle.textContent = 'Apple Pay live checkout is currently disabled for this store.';
                    walletErrorTitle.textContent = 'Enable live payment to activate Apple Pay';
                    walletErrorMessage.textContent = 'Apple Pay is in fallback mode. Turn on live payment in your PayPal Pro setup before offering Apple Pay to customers.';
                } else {
                    selectedWalletLabel.textContent = 'Google Pay selected';
                    checkoutTitle.textContent = 'Google Pay unavailable';
                    checkoutSubtitle.textContent = 'Google Pay live checkout is currently disabled for this store.';
                    walletErrorTitle.textContent = 'Enable live payment to activate Google Pay';
                    walletErrorMessage.textContent = 'Google Pay is in fallback mode. Turn on live payment in your PayPal Pro setup before offering Google Pay to customers.';
                }
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
                walletError.classList.add('hidden');
            });

            applePayClose.addEventListener('click', () => closeModal(applePayModal));
            applePayCancel.addEventListener('click', () => closeModal(applePayModal));
            applePayContinue.addEventListener('click', () => {
                closeModal(applePayModal);
                openWalletError('apple');
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
                openWalletError('google');
            });
            googlePayModal.addEventListener('click', (event) => {
                if (event.target === googlePayModal) {
                    closeModal(googlePayModal);
                }
            });

            if (preselectedWallet === 'applepay') {
                openModal(applePayModal);
            } else if (preselectedWallet === 'googlepay') {
                openModal(googlePayModal);
            }
        })();
    </script>
@endscript
