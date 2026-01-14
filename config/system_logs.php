<?php

return [

    'events' => [

        'auth' => [
            'login_attempt' => 'A login attempt was made.',
            'login_success' => 'User logged in successfully.',
            'login_failed'  => 'User login failed.',
            'logout'        => 'User logged out.',
        ],

        'booking' => [
            'created' => 'A new booking has been created.',
            'updated' => 'Booking information was updated.',
            'deleted' => 'Booking has been deleted.',
            'restored' => 'Booking restored from trash.',
        ],

        'service' => [
            'created' => 'A new service was added.',
            'updated' => 'Service information updated.',
            'deleted' => 'Service has been removed.',
        ],

    ],

    // Human-friendly messages per admin route (used when event_type === 'admin_route')
    'admin_route' => [
        // Auth / dashboard
        'admin.login' => 'Admin viewed login page.',
        'admin.dashboard' => 'Viewed admin dashboard.',
        'admin.logout' => 'Admin logged out.',

        // Logs
        'admin.logs.index' => 'Viewed system logs.',
        'admin.logs.clear' => 'Cleared all system logs.',

        // Services (resource)
        'admin.services.index' => 'Viewed services list.',
        'admin.services.create' => 'Opened create service form.',
        'admin.services.store' => 'Created a new service.',
        'admin.services.show' => 'Viewed service details.',
        'admin.services.edit' => 'Opened edit service form.',
        'admin.services.update' => 'Updated service information.',
        'admin.services.destroy' => 'Deleted a service.',

        // Service types
        'admin.service-types.index' => 'Viewed service types list.',
        'admin.service-types.create' => 'Opened create service type form.',
        'admin.service-types.store' => 'Created a new service type.',
        'admin.service-types.show' => 'Viewed service type details.',
        'admin.service-types.edit' => 'Opened edit service type form.',
        'admin.service-types.update' => 'Updated service type.',
        'admin.service-types.destroy' => 'Deleted a service type.',

        // Service categories
        'admin.service-categories.index' => 'Viewed service categories list.',
        'admin.service-categories.create' => 'Opened create service category form.',
        'admin.service-categories.store' => 'Created a new service category.',
        'admin.service-categories.show' => 'Viewed service category details.',
        'admin.service-categories.edit' => 'Opened edit service category form.',
        'admin.service-categories.update' => 'Updated service category.',
        'admin.service-categories.destroy' => 'Deleted a service category.',

        // Areas
        'admin.areas.index' => 'Viewed areas list.',
        'admin.areas.create' => 'Opened create area form.',
        'admin.areas.store' => 'Created a new area.',
        'admin.areas.show' => 'Viewed area details.',
        'admin.areas.edit' => 'Opened edit area form.',
        'admin.areas.update' => 'Updated area.',
        'admin.areas.destroy' => 'Deleted an area.',

        // Your items / dry clean
        'admin.your-items.index' => 'Viewed items list.',
        'admin.your-items.create' => 'Opened create item form.',
        'admin.your-items.store' => 'Created a new item.',
        'admin.your-items.show' => 'Viewed item details.',
        'admin.your-items.edit' => 'Opened edit item form.',
        'admin.your-items.update' => 'Updated item.',
        'admin.your-items.destroy' => 'Deleted an item.',

        // Bookings and booking actions
        'admin.bookings.index' => 'Viewed bookings list.',
        'admin.bookings.show' => 'Viewed booking details.',
        'admin.bookings.update' => 'Updated booking.',
        'admin.bookings.destroy' => 'Deleted booking.',
        'admin.bookings.trashed' => 'Viewed trashed bookings.',
        'admin.bookings.export' => 'Exported bookings.',
        'admin.bookings.restore' => 'Restored booking from trash.',
        'admin.bookings.search' => 'Searched bookings.',
        'admin.bookings.assignLab' => 'Assigned lab to booking.',
        'admin.bookings.arrivedAtLab' => 'Marked booking arrived at lab.',
        'admin.bookings.pickedFromLab' => 'Marked booking picked from lab.',
        'admin.bookings.driverCollected' => 'Marked booking collected by driver.',
        'admin.bookings.assignDriver' => 'Assigned driver to booking.',
        'admin.bookings.assignCar' => 'Assigned car to booking.',

        // Home banners
        'admin.home-banners.index' => 'Viewed home banners.',
        'admin.home-banners.create' => 'Opened create home banner form.',
        'admin.home-banners.store' => 'Created a home banner.',
        'admin.home-banners.show' => 'Viewed home banner.',
        'admin.home-banners.edit' => 'Edited home banner.',
        'admin.home-banners.update' => 'Updated home banner.',
        'admin.home-banners.destroy' => 'Deleted home banner.',

        // Admins management
        'admin.admins.index' => 'Viewed admin users list.',
        'admin.admins.create' => 'Opened create admin form.',
        'admin.admins.store' => 'Created an admin user.',
        'admin.admins.show' => 'Viewed admin user details.',
        'admin.admins.edit' => 'Opened edit admin form.',
        'admin.admins.update' => 'Updated admin user.',
        'admin.admins.destroy' => 'Deleted admin user.',

        // Drivers
        'admin.drivers.index' => 'Viewed drivers list.',
        'admin.drivers.create' => 'Opened create driver form.',
        'admin.drivers.store' => 'Created a driver.',
        'admin.drivers.show' => 'Viewed driver details.',
        'admin.drivers.edit' => 'Opened edit driver form.',
        'admin.drivers.update' => 'Updated driver.',
        'admin.drivers.destroy' => 'Deleted driver.',
        'admin.drivers.assignService' => 'Assigned service to driver.',
        'admin.drivers.removeService' => 'Removed service from driver.',

        // Cars additional service
        'admin.cars-additional-service.index' => 'Viewed extra car services.',
        'admin.cars-additional-service.create' => 'Opened create extra service form.',
        'admin.cars-additional-service.store' => 'Created extra car service.',
        'admin.cars-additional-service.show' => 'Viewed extra service.',
        'admin.cars-additional-service.edit' => 'Edited extra service.',
        'admin.cars-additional-service.update' => 'Updated extra service.',
        'admin.cars-additional-service.destroy' => 'Deleted extra service.',

        // Drivers vehicles
        'admin.driver-vehicles.index' => 'Viewed driver vehicles.',
        'admin.driver-vehicles.create' => 'Opened create driver vehicle form.',
        'admin.driver-vehicles.store' => 'Created driver vehicle.',
        'admin.driver-vehicles.show' => 'Viewed driver vehicle.',
        'admin.driver-vehicles.edit' => 'Edited driver vehicle.',
        'admin.driver-vehicles.update' => 'Updated driver vehicle.',
        'admin.driver-vehicles.destroy' => 'Deleted driver vehicle.',

        // Car wash drivers
        'admin.car-wash-drivers.index' => 'Viewed car wash drivers list.',
        'admin.car-wash-drivers.create' => 'Opened create car wash driver form.',
        'admin.car-wash-drivers.store' => 'Created car wash driver.',
        'admin.car-wash-drivers.show' => 'Viewed car wash driver.',
        'admin.car-wash-drivers.edit' => 'Edited car wash driver.',
        'admin.car-wash-drivers.update' => 'Updated car wash driver.',
        'admin.car-wash-drivers.destroy' => 'Deleted car wash driver.',

        // Partials & AJAX endpoints
        'admin.partials.area_prices' => 'Opened area prices partial.',
        'admin.partials.car_timeline_data' => 'Requested car timeline data.',
        'admin.bookings.json' => 'Requested booking JSON for modal.',

        // Vehicle timeline
        'admin.vehicle-timeline.full' => 'Viewed vehicle timeline.',
        'admin.vehicle-timeline.export' => 'Exported vehicle timeline.',

        // FCM token endpoints
        'admin.fcm-token.store' => 'Registered admin FCM token.',
        'admin.fcm-token.debug' => 'Debugged admin FCM token.',

        // Users
        'admin.users.index' => 'Viewed users list.',
        'admin.users.bookings' => 'Viewed user bookings.',

        // Labs
        'admin.labs.index' => 'Viewed labs list.',
        'admin.labs.create' => 'Opened create lab form.',
        'admin.labs.store' => 'Created lab.',
        'admin.labs.show' => 'Viewed lab.',
        'admin.labs.edit' => 'Edited lab.',
        'admin.labs.update' => 'Updated lab.',
        'admin.labs.destroy' => 'Deleted lab.',

        // Other admin resources (generic messages)
        'admin.home-banners' => 'Managed home banners.',
        'admin.payment-methods' => 'Viewed payment methods.',
        'admin.settings' => 'Updated settings.',

        // Fallback note: you can add more route-specific messages here as needed
    ],

];

