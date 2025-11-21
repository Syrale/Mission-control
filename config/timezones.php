<?php

return [
    'list' => [
        'UTC'                  => 'UTC (Universal)',
        
        // Fixed Timezones (Safe to put the offset)
        'Asia/Manila'          => 'UTC+8 Manila',
        'Asia/Singapore'       => 'UTC+8 Singapore',
        'Asia/Tokyo'           => 'UTC+9 Tokyo',
        'Asia/Dubai'           => 'UTC+4 Dubai',
        
        // DST Timezones (Better to use codes like PST/EST)
        'America/Los_Angeles'  => 'PST/PDT (Pacific)', // or 'UTC-8/7 Pacific'
        'America/New_York'     => 'EST/EDT (Eastern)', // or 'UTC-5/4 Eastern'
        'Europe/London'        => 'GMT/BST (London)',
        'Europe/Berlin'        => 'CET/CEST (Berlin)',
        'Australia/Sydney'     => 'AEDT (Sydney)',
    ]
];