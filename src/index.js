import { decodeEntities } from '@wordpress/html-entities';
import React, { useState, useEffect } from "@wordpress/element";

const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { getSetting } = window.wc.wcSettings;
const settings = getSetting('pagado_data', {});
const label = decodeEntities(settings.title);

const server = 'https://pagado.io';

const Content = () => {
    return decodeEntities(settings.description || '');
};

const PagadoIframeContent = (props) => {
    useEffect(() => {
        jQuery.ajax({
            method: 'post',
            url: '/?wc-ajax=get_pagado_data',
        }).done(function (res, status, xhr) {
            if (status === 'success') {
                initializeIframe(res.data);
            }
        }).fail(function (xhr, status, error) {
            console.log(error);
        });
    }, []);

    console.log(props);

    return (
        <>
            <p>{decodeEntities(settings.description || '')}</p>
            <div id="pagado-checkout-wrapper">
                <iframe
                    id="pagado-checkout-iframe"
                    src="https://pagado.io/checkout-buttons"
                    name="pagado_checkout_iframe"
                    height="100%"
                    width="100%"
                    title="Pagado Checkout"
                    style={{ border: 'none' }} />
            </div>
        </>
    );
}

const Label = (props) => {
    const { PaymentMethodLabel } = props.components;
    return <PaymentMethodLabel text={label} />
};

registerPaymentMethod({
    name: "pagado",
    label: <Label />,
    content: <PagadoIframeContent />,
    edit: <Content />,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    }
});

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
        iframeWindow.postMessage('loaded', server);
    }
    let checkoutWindow;

    jQuery(window).on('message', function (e) {
        e = e.originalEvent;

        if (e.origin === server) {
            if (e.data) {
                const eventData = JSON.parse(e.data);

                // console.log(eventData);
                // return;

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
                    const transactionIdHiddenField = checkoutForm.find('#pagado_data');

                    if (transactionIdHiddenField.length) {
                        transactionIdHiddenField.remove();
                    }

                    if (checkoutWindow && !checkoutWindow.closed) {
                        checkoutWindow.close();

                        checkoutForm.append(`<input type='hidden' id='pagado_data' name='pagado_data' value=${JSON.stringify(eventData)}>`);

                        $('#place_order').click();
                    }
                }
            }
        }
    });
}
