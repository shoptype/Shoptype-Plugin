const RAZORPAY_JS_URL = `https://checkout.razorpay.com/v1/checkout.js`;
const STRIPE_JS_URL = `https://js.stripe.com/v3/`;
const AUTHORIZE_SANDBOX_JS_URL = `https://jstest.authorize.net/v1/Accept.js`;
const AUTHORIZE_PROD_JS_URL = `https://js.authorize.net/v1/Accept.js`;

const initSTPayment = async (checkoutId, apiUrl, apiKey, callbackFunc, useAuthNetSandbox = false) => {
    if (!checkoutId || !apiUrl || !apiKey || !callbackFunc) {
        callbackFunc({ status: 'failed', message: 'Error while initiating payment' });
        return;
    }

    const response = await fetch(`${apiUrl}/checkout/${checkoutId}/payment`, {
        method: 'POST',
        headers: new Headers({
            "Content-Type": 'application/json',
            "X-Shoptype-Api-Key": apiKey
        }),
        body: {}
    });

    const paymentDetails = await response.json();

    switch (paymentDetails.method) {

        case 'razorpay':
            await loadScriptAsync(paymentDetails.method, RAZORPAY_JS_URL);
            await initRazorpayPayment(paymentDetails, checkoutId, callbackFunc);
            break;

        case 'stripe':
            await loadScriptAsync(paymentDetails.method, STRIPE_JS_URL);
            await initStripePayment(paymentDetails, callbackFunc);
            break;

        case 'authorize':
            if (useAuthNetSandbox) {
                //pass useAuthNetSandbox as true to use AcceptJs sandbox Js
                await loadScriptAsync(paymentDetails.method, AUTHORIZE_SANDBOX_JS_URL);
            } else {
                await loadScriptAsync(paymentDetails.method, AUTHORIZE_PROD_JS_URL)
            }

            await initAuthorizenetPayment(paymentDetails, checkoutId, apiKey, apiUrl, callbackFunc);
            break;

        default:
            callbackFunc({ status: 'failed', message: 'Error while initiating payment' });
    }
}

window.initSTPayment = initSTPayment;

const initAuthorizenetPayment = async (
    paymentDetails,
    checkoutId,
    apiKey,
    apiUrl,
    callbackFunc
) => {
    const currency = paymentDetails.amount.currency.toUpperCase();
    const amount = paymentDetails.amount.amount;
    const name = paymentDetails.billing_address.name;
    const phone = paymentDetails.billing_address.phone;
    const client_secret = paymentDetails.client_secret;
    let billingDetails = paymentDetails.billing_address || {};
    const setOutcome = (result) => {
        var waitElement = document.querySelector('.wait');
        var errorElement = document.querySelector('.error');
        waitElement.classList.remove('visible');
        errorElement.classList.remove('visible');
        if (result.inProgress) {
            waitElement.textContent = 'Please wait..';
            waitElement.classList.add('visible');
        } else if (result.error) {
            errorElement.textContent = result.error.message;
            errorElement.classList.add('visible');
        }
    }
    const existingModal = document.getElementById('authnet-payment-modal');
    if (existingModal) {
        existingModal.remove();
    }
    const modal = document.getElementById('payment-container');

    modal.innerHTML = `
        <div class="authnet-payment-modal micromodal-slide" id="authnet-payment-modal" aria-hidden="true">
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
            <h2 class="modal__title" id="modal-1-title">
                Powered by Authorize
            </h2>
            </header>
            <main class="modal__content" id="modal-1-content">
            <form class="authnet-payment-form">
                <div class="group">
                    <div class="card-grid">
                        <div>Card</div>
                        <div class="grid-item">
                            <input name="cardNumber" id="cardNumber" class="card-input" 
                            placeholder="Card Number" maxlength="19" onkeydown="jumpField(event, this, 'expMonth')" />
                        </div>
                        <div class="grid-item grid-item-row">
                            <input name="expMonth" id="expMonth" class="card-input" placeholder="MM" 
                            maxlength="2" onkeydown="jumpField(event, this, 'expYear', 'cardNumber')" />
                            <input name="expYear" id="expYear" class="card-input" placeholder="YY" maxlength="2"
                            onkeydown="jumpField(event, this, 'cardCode', 'expMonth')" />
                        </div>
                        <div div class="grid-item">
                            <input name="cardCode" id="cardCode" class="card-input" placeholder="CVC"
                            maxlength="4" onkeydown="jumpField(event, this, null, 'expYear')" />
                        </div>
                    </div>
                    <input type="hidden" name="dataValue" id="dataValue" />
                    <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
                </div>
                <button class="authorize-payement-btn" type="submit">Pay ${amount} ${currency}</button>
                <div class="outcome">
                <div class="error"></div>
                <div class="wait">
                </div>
                </div>
            </form>
            </main>
        </div>
    </div>`;

    document.body.appendChild(modal);

    document.querySelector('.authnet-payment-form').addEventListener('submit', (e) => {
        e.preventDefault();
        setOutcome({ inProgress: true });
        document.querySelector(".authorize-payement-btn").disabled = true;
        var authData = {};
        authData.clientKey = paymentDetails.client_secret;
        authData.apiLoginID = paymentDetails.id;
        var cardData = {};
        cardData.cardNumber = document.getElementById("cardNumber").value.replaceAll(' ', '');
        cardData.month = document.getElementById("expMonth").value;
        cardData.year = document.getElementById("expYear").value;
        cardData.cardCode = document.getElementById("cardCode").value;
        var secureData = {};
        secureData.authData = authData;
        secureData.cardData = cardData;
        let responseHandler = async (response) => {

            if (response.messages.resultCode === "Error") {

                document.querySelector(".authorize-payement-btn").disabled = false;
                setOutcome({ error: { message: response.messages.message[0].text } });

            } else {
                const res = await fetch(`${apiUrl}/checkout/${checkoutId}/payment/confirm`, {
                    method: 'POST',
                    headers: new Headers({
                        "Content-Type": 'application/json',
                        "X-Shoptype-Api-Key": apiKey
                    }),
                    body: JSON.stringify({
                        "payment_id": response.opaqueData.dataDescriptor,
                        "signature": response.opaqueData.dataValue
                    })
                });

                const paymentConfirm = await res.json();

                document.querySelector(".authorize-payement-btn").disabled = false;

                if (paymentConfirm.payment_status == "success") {
                    setOutcome({ inProgress: false });
                    callbackFunc({ status: 'success', message: 'Payment Successful', transactionId: paymentConfirm.transaction_id });
                } else {
                    setOutcome({ error: { message: paymentConfirm.error.message } });
                    callbackFunc({ status: 'failed', message: 'Payment Failed' });
                }
            }
        }

        Accept.dispatchData(secureData, responseHandler);
    })

    document.getElementById('cardNumber').addEventListener('input', function () {
        let val = this.value;
        let newval = '';
        val = val.replace(/\s/g, '');

        for (let i = 0; i < val.length; i++) {
            if (i % 4 == 0 && i > 0) newval = newval.concat(' ');
            newval = newval.concat(val[i]);
        }

        this.value = newval;
    });

    let s = document.createElement("script");
    s.innerHTML = `
    function jumpField(e, elem, nextElemId, prevElemId = null) {
        let key = e.keyCode || e.charCode;
        if (key == 8 && elem.value.length === 0 && prevElemId) {
            document.getElementById(prevElemId).focus();
        }
        if (key != 8 && elem.value.length >= elem.maxLength && nextElemId) {
            document.getElementById(nextElemId).focus();
        }
    }`;
    document.head.appendChild(s);
};

const initRazorpayPayment = async (
    paymentDetails,
    checkoutId,
    callbackFunc
) => {
    let options = {
        "key": paymentDetails.public_key,
        "amount": paymentDetails.amount.amount,
        "currency": paymentDetails.amount.currency,
        "name": "Shoptype",
        "image": "https://app.shoptype.com/static/media/logo.636341ad.png",
        "order_id": paymentDetails.id,
        "handler": (response) => {
            if (response.razorpay_payment_id) {
                callbackFunc({ status: 'success', message: 'Payment Successful', transactionId: paymentDetails.id });
            } else {
                callbackFunc({ status: 'failed', message: 'Payment Failed', transactionId: paymentDetails.id });
            }
        },
        "modal": {
            "ondismiss": () => {
                callbackFunc({ status: 'closed' });
            }
        },
        "prefill": {
            "name": paymentDetails.billing_address.name,
            "email": paymentDetails.billing_address.email,
            "contact": paymentDetails.billing_address.phone
        },
        "notes": {
            "Checkout ID": checkoutId
        },
        "theme": {
            "color": '#B69903'
        }
    };

    let razorpayHandler = new Razorpay(options);
    razorpayHandler.open();
}

const initStripePayment = async (
    paymentDetails,
    callbackFunc
) => {
    let stripe = Stripe(paymentDetails.public_key);
    let elements = stripe.elements();

    const currency = paymentDetails.amount.currency.toUpperCase();
    const amount = paymentDetails.amount.amount;
    const name = paymentDetails.billing_address.name;
    const phone = paymentDetails.billing_address.phone;
    const client_secret = paymentDetails.client_secret;
    let billingDetails = paymentDetails.billing_address || {};

    const setOutcome = (result) => {
        var waitElement = document.querySelector('.wait');
        var errorElement = document.querySelector('.error');
        waitElement.classList.remove('visible');
        errorElement.classList.remove('visible');

        if (result.inProgress) {
            waitElement.textContent = 'Please wait..';
            waitElement.classList.add('visible');
        } else if (result.error) {
            errorElement.textContent = result.error.message;
            errorElement.classList.add('visible');
        }
    }

    const existingModal = document.getElementById('stripe-payment-modal');
    if (existingModal) {
        existingModal.remove();
    }

    const modal = document.getElementById('payment-container');
    modal.innerHTML = `
        <div class="stripe-payment-modal micromodal-slide" id="stripe-payment-modal" aria-hidden="true">
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
            </header>
            <main class="modal__content" id="modal-1-content">
            <form class="stripe-payment-form">
                <div class="group">
                <label>
                    <span>Name</span>
                    <input name="cardholder-name" value="${name || ''}" class="field stripe-name-input" placeholder="Jane Doe" />
                </label>
                <label>
                    <span>Phone</span>
                    <input type="number" class="field stripe-phone-input" value="${phone || ''}" placeholder="(123) 456-7890" type="tel" />
                </label>
                </div>
                <div class="group">
                <label>
                    <span>Card</span>
                    <div id="card-element" class="field"></div>
                </label>
                </div>
                <button class="stripe-pay-btn" type="submit">Pay ${amount} ${currency}</button>
                <div class="outcome">
                <div class="error"></div>
                <div class="wait">
                </div>
                </div>
            </form>
            </main>
        </div>
    </div>`;

    let card = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                iconColor: '#F99A52',
                color: '#32315E',
                lineHeight: '48px',
                fontWeight: 400,
                fontFamily: '"Open Sans", "Helvetica Neue", "Helvetica", sans-serif',
                fontSize: '15px',

                '::placeholder': {
                    color: '#CFD7DF',
                }
            },
        }
    });

    card.on('change', (event) => {
        setOutcome(event);
    });

    card.mount("#card-element");

    document.querySelector('.stripe-payment-form').addEventListener('submit', (e) => {
        e.preventDefault();
        document.querySelector('.stripe-pay-btn').disabled = true;
        setOutcome({ inProgress: true });

        const nameInput = document.querySelector('.stripe-name-input').value;
        const phoneInput = document.querySelector('.stripe-phone-input').value;
        if (!nameInput || (/\d/.test(nameInput))) {
            setOutcome({ error: { message: 'Please enter a valid Name' } });
            document.querySelector('.stripe-pay-btn').disabled = false;
            return;
        }

        if (!phoneInput || (phoneInput.length > 10 || (phoneInput.length < 10 && phoneInput.length > 0))) {
            setOutcome({ error: { message: 'Please enter a valid Phone number' } });
            document.querySelector('.stripe-pay-btn').disabled = false;
            return;
        }

        stripe.confirmCardPayment(client_secret, {
            payment_method: {
                type: 'card',
                card: card,
                billing_details: {
                    name: billingDetails.name || '',
                    email: billingDetails.email || '',
                    address: {
                        city: billingDetails.city || '',
                        line1: billingDetails.street1 || '',
                        state: billingDetails.state || '',
                        postal_code: billingDetails.postalCode || '',
                    },
                }
            }
        }).then((result) => {
            if (result.error) {
                setOutcome(result);
                document.querySelector('.stripe-pay-btn').disabled = false;
            } else {
                if (result.paymentIntent.status === 'succeeded') {
                    callbackFunc({ status: 'success', message: 'Payment Successful', transactionId: paymentDetails.id });
                } else {
                    callbackFunc({ status: 'failed', message: 'Payment Failed', transactionId: paymentDetails.id });
                }

                setOutcome({ inProgress: false });
                document.querySelector('.stripe-pay-btn').disabled = false;
            }
        });
    });
};

const loadScriptAsync = (method, url) => {
    return new Promise((resolve, reject) => {
        const scriptTagId = `${method}-payment-handler-script`;
        let existingScript = document.getElementById(scriptTagId);
        if (existingScript) {
            resolve();
            return;
        }

        var tag = document.createElement('script');
        tag.id = scriptTagId;
        tag.src = url;
        tag.async = true;
        tag.onload = () => {
          resolve();
        };

        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
      });
}