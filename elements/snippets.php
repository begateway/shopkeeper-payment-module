<?php

return [
    'begateway' => [
        'file' => 'snippet.begateway',
        'description' => 'Begateway snippet',
        'properties' => [
            'shopId' => [
                'type' => 'textfield',
                'desc' => 'Shop ID',
                'value' => '361',
            ],
            'secretKey' => [
                'type' => 'textfield',
                'desc' => 'Shop Secret Key',
                'value' => 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d',
            ],
            'paymentDomain' => [
                'type' => 'textfield',
                'desc' => 'Payment page domain (https://checkout.begateway.com)',
                'value' => 'https://checkout.begateway.com',
            ],
            'test' => [
                'type' => 'combo-boolean',
                'desc' => 'Test Mode',
                'value' => true,
            ],
            'currency' => [
                'type' => 'textfield',
                'maxlength' => 3,
                'desc' => 'Default currency CODE for payment',
                'value' => 'BYN',
            ],
            'currencyMode' => [
                'type' => 'list',
                'options' => [
                    ['value' => 'default', 'text' => 'Payment in default currency'],
                    ['value' => 'usercurrency', 'text' => 'Payment in user\'s currency'],
                ],
                'desc' => 'Select in what currency to send the user for payment, in the default currency for your store or in the currency chosen by the user (valid if the multicurrency plugin is enabled)',
                'value' => 'default',
            ],
            'redirectMode' => [
                'type' => 'list',
                'options' => [
                    ['text' => 'Redirect Immediately', 'value' => 'redirect'],
                    ['text' => 'After Invoice Page', 'value' => 'invoice'],
                ],
                'desc' => 'Redirect to Payment Page',
                'value' => 'redirect',
            ],
            'chunkOrderDetail' => [
                'type' => 'textfield',
                'desc' => 'Chunk Order Detail',
                'value' => 'orderDetail',
            ],
            'chunkOrderDetailRow' => [
                'type' => 'textfield',
                'desc' => 'Chunk Order Detail Row',
                'value' => 'orderDetailRow',
            ],
            'chunkOrderContactsRow' => [
                'type' => 'textfield',
                'desc' => 'Chunk Order Contacts Row',
                'value' => 'orderContactsRow',
            ],
            'chunkOrderTryAgainDetail' => [
                'type' => 'textfield',
                'desc' => 'Chunk Order Try Pay Again Detail',
                'value' => 'orderTryAgainDetail',
            ],
            'chunkOrderSuccessPaid' => [
                'type' => 'textfield',
                'desc' => 'Chunk Order Success Paid',
                'value' => 'orderSuccessPaid',
            ],
            'pagePayment' => [
                'type' => 'numberfield',
                'desc' => 'ID of the Begateway Payment Page',
                'value' => 10,
            ],
            'pageSuccess' => [
                'type' => 'numberfield',
                'desc' => 'ID of the Begateway Success Page or Other Success Page',
                'value' => 13,
            ],
            'pageFailure' => [
                'type' => 'numberfield',
                'desc' => 'ID of the Begateway Failure Page',
                'value' => 11,
            ],
            'pageNotify' => [
                'type' => 'numberfield',
                'desc' => 'ID of the Begateway Notify Page',
                'value' => 12,
            ],
            'statusNew' => [
                'type' => 'numberfield',
                'maxlength' => 2,
                'desc' => 'ID of the Order Status = New',
                'value' => 1,
            ],
            'statusAccept' => [
                'type' => 'numberfield',
                'maxlength' => 2,
                'desc' => 'ID of the Order Status = Accepted for payment',
                'value' => 2,
            ],
            'statusCancel' => [
                'type' => 'numberfield',
                'maxlength' => 2,
                'desc' => 'ID of the Order Status = Canceled',
                'value' => 5,
            ],
            'statusPaid' => [
                'type' => 'numberfield',
                'maxlength' => 2,
                'desc' => 'ID of the Order Status = Payment received',
                'value' => 6,
            ],
        ],
    ],
];
