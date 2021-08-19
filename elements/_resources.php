<?php

return [
    'web' => [
        'begateway-pay' => [
            'id' => null,
            'pagetitle' => 'Begateway Payment Page',
            'template' => 1,
            'isfolder' => 0,
            'published' => true,
            'hidemenu' => true,
            'content' => '[[!begateway? action=`payment`]]',
        ],
        'begateway-success' => [
            'id' => null,
            'pagetitle' => 'Begateway Success Page',
            'template' => 0,
            'isfolder' => 0,
            'published' => true,
            'hidemenu' => true,
            'content' => '[[!begateway? action=`success`]]',
        ],
        'begateway-fail' => [
            'id' => null,
            'pagetitle' => 'Begateway Failure Page',
            'template' => 0,
            'isfolder' => 0,
            'published' => true,
            'hidemenu' => true,
            'content' => '[[!begateway? action=`fail`]]',
        ],
        'begateway-notify' => [
            'id' => null,
            'pagetitle' => 'Begateway Notify Page',
            'template' => 0,
            'isfolder' => 0,
            'published' => true,
            'hidemenu' => true,
            'content' => '[[!begateway? action=`notify`]]',
        ],
    ],
];
