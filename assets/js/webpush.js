let bsn = require('bootstrap.native');

document.addEventListener('DOMContentLoaded', () => {
    let serverKey = window.webpushKey;
    let serverUrl = window.serverUrl;
    let serviceWorkerUrl = window.serviceWorkerUrl;

    const state = {
        enabled: 'enabled',
        disabled: 'disabled',
        notSupported: 'notSupported',
        notGranted: 'notGranted'
    };

    let pushModalSelector = '#modal_webpush';
    let enablePushBtnSelector = '#enable_webpush';
    let disablePushBtnSelector = '#disable_webpush';
    let enablePushSuccessSelector = '#enable_webpush_success';
    let disablePushSuccessSelector = '#disable_webpush_success';

    let stateEnabledSelector = '#push_status_enabled';
    let stateDisabledSelector = '#push_status_disabled';
    let stateNotSupportedSelector = '#push_not_supported';
    let stateNotGrantedSelector = '#push_not_granted';

    let stateSelectors = [
        stateEnabledSelector,
        stateDisabledSelector,
        stateNotSupportedSelector,
        stateNotGrantedSelector
    ];

    let enableLoadingSelector = '#enable_push_loading';
    let disableLoadingSelector = '#disable_push_loading';

    let notSupportedMessageSelector = '#push_not_supported_message';
    let notGrantedMessageSelector = '#push_not_granted_message';

    let showEnableButton = function() {
        document.querySelector(enablePushBtnSelector).classList.remove('d-none');
    };

    let showDisableButton = function() {
        document.querySelector(disablePushBtnSelector).classList.remove('d-none');
    };

    let hideEnableButton = function() {
        document.querySelector(enablePushBtnSelector).classList.add('d-none');
    };

    let hideDisableButton = function() {
        document.querySelector(disablePushBtnSelector).classList.add('d-none');
    };

    let showEnableLoading = function() {
        document.querySelector(enableLoadingSelector).classList.remove('d-none');
    };

    let hideEnableLoading = function() {
        document.querySelector(enableLoadingSelector).classList.add('d-none');
    };

    let showDisableLoading = function() {
        document.querySelector(disableLoadingSelector).classList.remove('d-none');
    };

    let hideDisableLoading = function() {
        document.querySelector(disableLoadingSelector).classList.add('d-none');
    };

    let showState = function(currentState) {
        stateSelectors.forEach(function(stateSelector) {
            document.querySelector(stateSelector).classList.add('d-none');
        });

        [ notGrantedMessageSelector, notSupportedMessageSelector ].forEach(function(messageSelector) {
            document.querySelector(messageSelector).classList.add('d-none');
        });

        let selector = null;
        let messageSelector = null;

        switch(currentState) {
            case state.enabled:
                selector = stateEnabledSelector;
                break;
            case state.disabled:
                selector = stateDisabledSelector;
                break;
            case state.notGranted:
                selector = stateNotGrantedSelector;
                messageSelector = notGrantedMessageSelector;
                break;
            case state.notSupported:
                selector = stateNotSupportedSelector;
                messageSelector = notSupportedMessageSelector;
                break;
        }

        if(currentState === state.enabled) {
            showDisableButton();
            hideEnableButton();
        } else if(currentState === state.disabled) {
            showEnableButton();
            hideDisableButton();
        }

        if(selector !== null) {
            document.querySelector(selector).classList.remove('d-none');
        }

        if(messageSelector !== null) {
            document.querySelector(selector).classList.remove('d-none');
        }
    };

    let modal = new bsn.Modal(pushModalSelector);
    document.querySelectorAll('[data-toggle=webpush_modal]').forEach(function(el) {
        el.addEventListener('click', function(event) {
            event.preventDefault();
            modal.show();
        });
    });

    if(!('serviceWorker') in navigator || !('PushManager' in window) || !('showNotification' in ServiceWorkerRegistration.prototype)) {
        console.error('Push Notification API is not supported by this browser.');
        showState(state.notSupported);
        return;
    }

    if(Notification.permission === 'denied') {
        console.error('Push Notifications are not allowed by this browser.');
        showState(state.notGranted);
        return;
    }

    showState(state.disabled);

    let sendSubscription = function(subscription, method) {
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

        return fetch(serverUrl, {
            method,
            body: JSON.stringify({
                subscription: {
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                        auth: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
                    },
                    contentEncoding
                },
                options: [ ]
            }),
        }).then(() => subscription);
    };

    let updateSubscription = function() {
        navigator.serviceWorker.ready
            .then(registration => registration.pushManager.getSubscription())
            .then(subscription => {
                if(!subscription) {
                    showState(state.disabled);
                    return;
                }

                return subscription;
                //return sendSubscription(subscription, 'PUT'); (currently not supported by bundle)
            })
            .then(subscription => subscription && showState(state.enabled))
            .catch(e => {
                console.error('Errow when updating subscription.', e);
            });
    };

    let checkNotificationPermission = function() {
        return new Promise((resolve, reject) => {
            if (Notification.permission === 'denied') {
                return reject(new Error('Push messages are blocked.'));
            }

            if (Notification.permission === 'granted') {
                return resolve();
            }

            if (Notification.permission === 'default') {
                return Notification.requestPermission().then(result => {
                    if (result !== 'granted') {
                        reject(new Error('Bad permission result'));
                    }

                    resolve();
                });
            }
        });
    };

    let enableSubscription = function() {
        showEnableLoading();

        return checkNotificationPermission()
            .then(() => navigator.serviceWorker.ready)
            .then(registration => registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: serverKey
            }))
            .then(subscription => sendSubscription(subscription, 'POST'))
            .then(subscription => {
                if(subscription) {
                    showState(state.enabled);
                }

                hideEnableLoading();
            })
            .catch(e => {
                console.error('An error occurred while subscribing.', e);
                if(Notification.permission === 'denied') {
                    showState(state.notGranted);
                } else {
                    showState(state.disabled);
                    hideEnableLoading();
                }
            });
    };

    let disableSubscription = function() {
        showDisableLoading();

        navigator.serviceWorker.ready
            .then(registration => registration.pushManager.getSubscription())
            .then(subscription => {
                if(!subscription) {
                    showState(state.disabled);
                    return;
                }

                return sendSubscription(subscription, 'DELETE');
            })
            .then(subscription => subscription.unsubscribe())
            .then(() => {
                showState(state.disabled);
                hideDisableLoading();
            })
            .catch(e => {
                console.error('An error occurred while unsubscribing.', e);
                showState(state.disabled);
                hideDisableLoading();
            });
    };

    navigator.serviceWorker.register(serviceWorkerUrl, {
        scope: '/'
    }).then(
        registration => {
            updateSubscription();

            if(registration) {
               registration.update();
            }
        },
        e => {
            console.error('Failed to register service worker.', e);
            showState(state.notSupported);
        }
    );

    document.querySelector(enablePushBtnSelector).addEventListener('click', function(event) {
        event.preventDefault();
        enableSubscription();
    });

    document.querySelector(disablePushBtnSelector).addEventListener('click', function(event) {
        event.preventDefault();
        disableSubscription();
    });
});