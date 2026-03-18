/* Service Worker – Notifications Push (Unions Agency) */
self.addEventListener('push', function (event) {
    if (!event.data) return;
    var data = {};
    try {
        data = event.data.json();
    } catch (e) {
        data = { title: 'Unions Agency', body: event.data.text() || 'Nouvelle notification' };
    }
    var title = data.title || 'Unions Agency';
    var body = data.body || '';
    var url = (data.url || '/').toString();
    var options = {
        body: body,
        icon: '/images/icon-192.png',
        badge: '/images/badge-72.png',
        tag: data.tag || 'push-' + Date.now(),
        requireInteraction: false,
        data: { url: url, logId: data.logId || null }
    };
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    var url = event.notification.data && event.notification.data.url;
    if (url) {
        event.waitUntil(
            clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
                for (var i = 0; i < clientList.length; i++) {
                    if (clientList[i].url && 'focus' in clientList[i]) {
                        clientList[i].navigate(url);
                        return clientList[i].focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
        );
    }
});
