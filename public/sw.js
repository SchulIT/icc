self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const sendNotification = data => {
        return self.registration.showNotification(data.title, data.options);
    };

    if (event.data) {
        event.waitUntil(sendNotification(event.data.json()));
    }
});