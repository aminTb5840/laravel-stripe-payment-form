<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment - Stripe</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .StripeElement {
            box-sizing: border-box;
            height: 40px;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background-color: white;
            transition: all 0.3s ease;
        }
        .StripeElement--focus {
            border-color: #0078b7;
            box-shadow: 0 0 0 1px #0078b7;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">
<div class="w-full max-w-md">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-[#0078b7] px-6 py-4">
            <h1 class="text-xl font-bold text-white">Complete Your Payment</h1>
        </div>
        <!-- Payment Container -->
        <div id="payment-container">
            <div class="p-6">
                <!-- Order Summary -->
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Order</span>
                        <span class="font-medium">ORD-{{ strtoupper(Str::random(8)) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Amount Due</span>
                        <span class="text-xl font-bold text-[#0078b7]">{{ number_format($debt, 2) }} USD</span>
                    </div>
                </div>
                <!-- Payment Form -->
                <form action="{{ route('payment.process') }}" method="POST" id="payment-form">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Card Information</label>
                        <div id="card-element" class="StripeElement"></div>
                        <div id="card-errors" class="text-red-500 text-xs mt-2" role="alert"></div>
                    </div>
                    <button type="submit" id="submit-button"
                            class="w-full bg-[#0078b7] hover:bg-[#0066a0] text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                        Pay {{ number_format($debt, 2) }} USD
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <div class="mt-4 text-center text-xs text-gray-500">
        <p>Secure payment processed by Stripe</p>
    </div>
</div>
@if(session('success'))
    <div id="alert-success-{{ md5(session('success')) }}"
         class="alert-container fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-md ring-1 ring-black ring-opacity-5 overflow-hidden opacity-0 translate-y-2 transition-all duration-200"
         style="transform: translateY(0);">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="dismissAlert(this.closest('.alert-container'))"
                            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <!-- X icon -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-gray-200 h-1 w-full">
            <div id="progress-success-{{ md5(session('success')) }}" class="h-1 transition-all duration-linear bg-green-500">
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertElement = document.getElementById('alert-success-{{ md5(session('success')) }}');
            if (alertElement) {
                setTimeout(() => {
                    alertElement.classList.remove('opacity-0', 'translate-y-2');
                    alertElement.classList.add('opacity-100', 'translate-y-0');
                }, 50);
                const progressBar = document.getElementById('progress-success-{{ md5(session('success')) }}');
                if (progressBar) {
                    void progressBar.offsetWidth;
                    progressBar.style.transitionDuration = '5000ms';
                    progressBar.style.width = '0%';
                }
                setTimeout(() => {
                    dismissAlert(alertElement);
                }, {{ 5000 }});
            }
        });
        function dismissAlert(alertElement) {
            if (alertElement) {
                alertElement.classList.remove('opacity-100', 'translate-y-0');
                alertElement.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    if (alertElement.parentNode) {
                        alertElement.parentNode.removeChild(alertElement);
                    }
                }, 200);
            }
        }
    </script>
@endif
@if(session('error'))
    <div id="alert-error-{{ md5(session('error')) }}"
         class="alert-container fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-md ring-1 ring-black ring-opacity-5 overflow-hidden opacity-0 translate-y-2 transition-all duration-200"
         style="transform: translateY(0);">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ session('error') }}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button onclick="dismissAlert(this.closest('.alert-container'))"
                            class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <!-- X icon -->
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-gray-200 h-1 w-full">
            <div id="progress-error-{{ md5(session('error')) }}" class="h-1 transition-all duration-linear bg-green-500">
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertElement = document.getElementById('alert-error-{{ md5(session('error')) }}');
            if (alertElement) {
                setTimeout(() => {
                    alertElement.classList.remove('opacity-0', 'translate-y-2');
                    alertElement.classList.add('opacity-100', 'translate-y-0');
                }, 50);
                const progressBar = document.getElementById('progress-error-{{ md5(session('error')) }}');
                if (progressBar) {
                    void progressBar.offsetWidth;
                    progressBar.style.transitionDuration = '5000ms';
                    progressBar.style.width = '0%';
                }
                setTimeout(() => {
                    dismissAlert(alertElement);
                }, {{ 5000 }});
            }
        });
        function dismissAlert(alertElement) {
            if (alertElement) {
                alertElement.classList.remove('opacity-100', 'translate-y-0');
                alertElement.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => {
                    if (alertElement.parentNode) {
                        alertElement.parentNode.removeChild(alertElement);
                    }
                }, 200);
            }
        }
    </script>
@endif
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ config('services.stripe.key') }}');
    var elements = stripe.elements();
    var card = elements.create('card', {
        style: {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {color: '#aab7c4'}
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });

    card.mount('#card-element');

    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    var form = document.getElementById('payment-form');
    var submitButton = document.getElementById('submit-button');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
                submitButton.disabled = false;
                submitButton.textContent = 'Pay {{ number_format($debt, 2) }} USD';
            } else {
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', result.token.id);
                form.appendChild(hiddenInput);
                form.submit();
            }
        });
    });
</script>
</body>
</html>
