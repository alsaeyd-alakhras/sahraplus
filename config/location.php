<?php

return [

    'driver' => Stevebauman\Location\Drivers\IpApi::class,

    'fallbacks' => [
        Stevebauman\Location\Drivers\Ip2locationio::class,
        Stevebauman\Location\Drivers\IpInfo::class,
        Stevebauman\Location\Drivers\GeoPlugin::class,
    ],

    'position' => Stevebauman\Location\Position::class,

    'http' => [
        'timeout' => 3,
        'connect_timeout' => 3,
    ],

    'testing' => [
        'ip' => '8.8.8.8',
        'enabled' => true,
    ],

];