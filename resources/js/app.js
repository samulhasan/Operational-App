import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.Echo.channel('device-status')
    .listen('DeviceStatusChanged', (e) => {
        alert(`Device ${e.device.device_id} status changed: ${e.device.status.is_online ? 'Online' : 'Offline'}`);
    });
