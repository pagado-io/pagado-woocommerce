(function ($) {
    'use strict';

    var server = 'https://pagado.io';

    $(document).ready(function () {
        $('body').on('updated_checkout', function () {
            updateCheckoutButtonsVisibility();
        });

        $(window).load(function () {
            updateCheckoutButtonsVisibility();
        });

        $('form.checkout').on('change', 'input[name="payment_method"]', function () {
            updateCheckoutButtonsVisibility();
        });

        const iframe = document.getElementById('pagado-checkout-iframe');

        if (!iframe) {
            return;
        }

        $.ajax({
            method: 'post',
            url: '/?wc-ajax=get_pagado_data',
        }).done(function (res, status, xhr) {
            if (status === 'success') {
                initializeIframe(res.data);
            }
        }).fail(function (xhr, status, error) {
            console.log(error);
        });
    });

    /**
     * Update payment gateway button and checkout buttons visibility
     * based on selected payment gateway.
     */
    function updateCheckoutButtonsVisibility() {
        const current = $('form[name="checkout"] input[name="payment_method"]:checked').val();
        const placeOrderBtn = $('button#place_order');
        const pagadoCheckout = $('#pagado-checkout-wrapper');

        if (current === 'pagado') {
            placeOrderBtn.addClass('pagado-hidden');
            pagadoCheckout.removeClass('pagado-hidden');
        } else {
            placeOrderBtn.removeClass('pagado-hidden');
            pagadoCheckout.addClass('pagado-hidden');
        }
    }

    /**
     * Initialize iframe with required data and take action
     * based on message communication between windows.
     *
     * @param {*} data Required data for the window
     * @returns
     */
    function initializeIframe(data) {
        const iframe = document.getElementById('pagado-checkout-iframe');

        if (!iframe) {
            return;
        }

        const iframeWindow = iframe.contentWindow;

        iframe.addEventListener('load', handleLoad, true);

        function handleLoad() {
            iframeWindow.postMessage('hi', server);
        }

        let checkoutWindow;

        $(window).on('message', function (e) {
            e = e.originalEvent;

            if (e.origin === server) {
                if (e.data) {
                    const eventData = JSON.parse(e.data);

                    if (eventData.target === 'checkout_button' && eventData.event === 'click') {
                        const urlData = {
                            origin: window.origin,
                            redirect: window.location.href,
                            variant: data.variant,
                            to: data.to,
                            price: data.price,
                            currency: data.currency,
                            version: data.version,
                        };

                        const urlParams = `currency=${urlData.currency}&price=${urlData.price}&to=${urlData.to}&redirect=${urlData.redirect}&variant=${urlData.variant}&version=${urlData.version}`

                        const settings = {
                            url: `${server}/checkout?${urlParams}`,
                            target: 'pagado-checkout-window',
                            width: 300,
                            height: 500,
                            left: 100,
                            top: 100,
                        };

                        settings.left = window.innerWidth / 2 - settings.width / 2;
                        settings.top = window.innerHeight / 2 - settings.height / 2;

                        const features = `width=${settings.width}, height=${settings.height}, left=${settings.left}, top=${settings.top}`;

                        checkoutWindow = window.open(settings.url, settings.target, features);
                    }

                    if (eventData.target === 'checkout_window' && eventData.event === 'checkout') {
                        const checkoutForm = $('form.checkout');
                        const transactionIdHiddenField = checkoutForm.find('#transaction_id');

                        if (transactionIdHiddenField.length) {
                            transactionIdHiddenField.remove();
                        }

                        if (checkoutWindow && !checkoutWindow.closed) {
                            checkoutWindow.close();

                            if (!transactionIdHiddenField.length) {

                                checkoutForm.append(`<input type='hidden' id='transaction_id' name='transaction_id' value=${eventData.id} type='hidden'>`);
                            }

                            $('#place_order').click();
                        }
                    }
                }
            }
        });
    }
})(jQuery);
