<?php

$GOOGLE_MAPS_API_KEY = 'your-key-here';
$CESIUM_AT = 'your-at-here';

//Let's make an array of interesting places we can visit.
$INTERESTING_PLACES = [
    [
        "label" => "Golden Gate View Point",
        'lat' => 37.80867,
        'lon' => -122.47544,
        'height' => 50,
        'heading' => -29,
        'ambient_audio' => 'city.mp3',
        'purchase_url' => false
    ],
    [
        "label" => "Eiffel Tower",
        'lat' => 48.85824,
        'lon' => 2.29452,
        'height' => 400,
        'heading' => -29,
        'ambient_audio' => 'city.mp3',
        'purchase_url' => 'https://www.toureiffel.paris/en/rates-opening-times'
    ],
    [
        "label" => "CN Tower",
        'lat' => 43.64257,
        'lon' => -79.38709,
        'height' => 500,
        'heading' => -29,
        'ambient_audio' => 'park.mp3',
        'purchase_url' => 'https://www.cntower.ca/plan-your-visit/tickets-and-hours/tickets'
    ],
    [
        "label" => "Niagara Falls",
        'lat' => 43.07924,
        'lon' => -79.07821,
        'height' => 130,
        'heading' => 159,
        'ambient_audio' => 'waterfall.mp3',
        'purchase_url' => 'https://www.niagarafallstourism.com/tickets/'
    ],
    [
        "label" => "Sydney Opera House",
        'lat' => -33.85620,
        'lon' => 151.21550,
        'height' => 30,
        'heading' => -29,
        'ambient_audio' => 'park.mp3',
        'purchase_url' => false
    ],
    [
        "label" => "Mt Everest Peak",
        'lat' => 27.98810,
        'lon' => 86.92499,
        'height' => 8849,
        'heading' => -29,
        'ambient_audio' => 'winter.mp3',
        'purchase_url' => false
    ],
    [
        "label" => "Matterhorn Glacier Paradise",
        'lat' => 45.93839,
        'lon' => 7.7300,
        'height' => 3983,
        'heading' => -29,
        'ambient_audio' => 'winter.mp3',
        'purchase_url' => 'https://www.matterhornparadise.ch/en/book/tickets/matterhorn-glacier-paradise'
    ]
];
