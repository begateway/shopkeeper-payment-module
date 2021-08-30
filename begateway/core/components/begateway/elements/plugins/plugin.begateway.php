<?php
/**
 * Plugin begateway
 *
 * @var modX $modx
 *
 * System event: OnSHKsaveOrder
 */
switch ($modx->event->name) {
    case 'OnSHKsaveOrder':
        $order = $modx->getObject('shk_order', $order_id);
        $order = $order->toArray();

        $_SESSION['shk_lastOrder']['delivery_price'] = $order['delivery_price'];
}
