<?php
/**
 * BeGateWay
 *
 * @var modX $modx
 * @var array $scriptProperties
 *
 * @package begateway
 */
//debug
error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set('log_errors','on');
ini_set('error_log', __DIR__ . 'begateway.error.log');

require_once(MODX_CORE_PATH.'components/begateway/begateway.class.php');

$modelpath = $modx->getOption('core_path') . 'components/shopkeeper3/model/';
$modx->addPackage( 'shopkeeper3', $modelpath );

if (isset($_REQUEST['payment']) && $_REQUEST['payment'] == 'begateway') {
    $payment_url = $modx->getOption('pagePayment', $scriptProperties, null);
    $modx->sendRedirect($modx->makeUrl($payment_url));
}

if (isset($scriptProperties['action'])) {
    $pk_obj = new BegatewayPayment($modx);
    $pk_obj->setParams(
        $modx->getOption('shopId', $scriptProperties, '361'),
        $modx->getOption('shopSecretKey', $scriptProperties, 'b8647b68898b084b836474ed8d61ffe117c9a01168d867f24953b776ddcb134d'),
        $modx->getOption('shopPublicKey', $scriptProperties, null),
        $modx->getOption('test', $scriptProperties, true),
        'https://' . $modx->getOption('paymentDomain', $scriptProperties, 'checkout.begateway.com'),
        $modx->getOption('pageSuccess', $scriptProperties, null),
        $modx->getOption('pageFailure', $scriptProperties, null),
        $modx->getOption('pageNotify', $scriptProperties, null),
        $modx->getOption('currencyMode', $scriptProperties, 'default'),
        $modx->getOption('currency', $scriptProperties, 'BYN')
    );

    switch ($scriptProperties['action']) {
        case 'payment':
            if (isset($_SESSION['shk_order_id'])) {
                $order_id = $_SESSION['shk_order_id'];
            } elseif (isset($_SESSION['shk_lastOrder']['id'])) {
                $order_id = $_SESSION['shk_lastOrder']['id'];
            } elseif (isset($_REQUEST['order_id'])) {
                $order_id = $_REQUEST['order_id'];
            }
            if (!isset($order_id)) {
                $errorCode = 400;
                $errorMessage = 'Bad Request';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $order = $modx->getObject('shk_order', $order_id);
            if (!$order) {
                $errorCode = 500;
                $errorMessage = 'No shk_order object found';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $orderStatus = $order->get('status');

            if ($orderStatus != $modx->getOption('statusNew', $scriptProperties, 1) &&
                $orderStatus != $modx->getOption('statusAccept', $scriptProperties, 2) &&
                $orderStatus != $modx->getOption('statusCancel', $scriptProperties, 5)) {
                    $errorCode = 500;
                    $errorMessage = 'The order has an inappropriate status';
                    return $pk_obj->setError($errorCode, $errorMessage);
            }

            $order = $order->toArray();
            $order['contacts'] = json_decode( $order['contacts'], true );

            $response = $pk_obj->getPayLink($order);

            if (isset($response->errors)) {
                $errorCode = 500;
                $errorMessage = 'Error Response: ' . $response->message;
                return $pk_obj->setError($errorCode, $errorMessage);
            } elseif (isset($response->error)) {
                $errorCode = 500;
                $errorMessage = 'Error Response: ' . $response->error;
                return $pk_obj->setError($errorCode, $errorMessage);
            } elseif (!isset($response->checkout->token)) {
                $errorCode = 500;
                $errorMessage = 'Error Response: ' . 'Token not be Null';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $orderChange = $modx->getObject('shk_order', $order_id);
            $orderChange->set('status', $modx->getOption('statusAccept', $scriptProperties, 2));
            $orderChange->save();
            $orderStatus = $orderChange->get('status');

            // if redirectMode == Redirect Immediately
            if ($modx->getOption('redirectMode', $scriptProperties, 'redirect') == 'redirect') {
                $modx->sendRedirect($response->checkout->redirect_url);
            }
            // or if redirectMode == After Invoice Page
            elseif ($modx->getOption('redirectMode', $scriptProperties, 'redirect') == 'invoice') {
                $orderDetailTpl = $modx->getOption('chunkOrderDetail', $scriptProperties, 'orderDetail');
                $orderDetailRowTpl = $modx->getOption('chunkOrderDetailRow', $scriptProperties, 'orderDetailRow');
                $orderContactsRowTpl = $modx->getOption('chunkOrderContactsRow', $scriptProperties, 'orderContactsRow');

                return $pk_obj->renderInvoice($order, $orderStatus, $response->checkout->redirect_url, $orderDetailTpl, $orderDetailRowTpl, $orderContactsRowTpl);
            }

            $errorCode = 500;
            $errorMessage = 'The snippet settings contain errors. Check snippet settings begateway.';
            return $pk_obj->setError($errorCode, $errorMessage);

        case 'fail':
            if (!isset($_REQUEST['order_id'])) {
                $errorCode = 400;
                $errorMessage = 'Bad Request';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $order_id = $_REQUEST['order_id'];

            $order = $modx->getObject('shk_order', $order_id);
            if (!$order) {
                $errorCode = 500;
                $errorMessage = 'No shk_order object found';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $orderStatus = $order->get('status');

            if ($orderStatus != $modx->getOption('statusNew', $scriptProperties, 1) &&
                $orderStatus != $modx->getOption('statusAccept', $scriptProperties, 2) &&
                $orderStatus != $modx->getOption('statusCancel', $scriptProperties, 5)) {
                    $errorCode = 500;
                    $errorMessage = 'The order has an inappropriate status';
                    return $pk_obj->setError($errorCode, $errorMessage);
            } else {
                $order->set('status', $modx->getOption('statusCancel', $scriptProperties, 5));
                $order->save();
                $orderStatus = $order->get('status');
            }

            $order = $order->toArray();
            $order['contacts'] = json_decode( $order['contacts'], true );

            $response = $pk_obj->getPayLink($order);

            if (isset($response->errors)) {
                $errorCode = 500;
                $errorMessage = 'Error Response: ' . $response->message;
                return $pk_obj->setError($errorCode, $errorMessage);
            } elseif (isset($response->error)) {
                $errorCode = 500;
                $errorMessage = 'Error Response: ' . $response->error;
                return $pk_obj->setError($errorCode, $errorMessage);
            } elseif (!isset($response->checkout->token)) {
                $errorCode = 500;
                $errorMessage = 'Error Response: ' . 'Token not be Null';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $orderDetailTpl = $modx->getOption('chunkOrderTryAgainDetail', $scriptProperties, 'orderTryAgainDetail');
            $orderDetailRowTpl = $modx->getOption('chunkOrderDetailRow', $scriptProperties, 'orderDetailRow');
            $orderContactsRowTpl = $modx->getOption('chunkOrderContactsRow', $scriptProperties, 'orderContactsRow');

            return $pk_obj->renderInvoice($order, $orderStatus, $response->checkout->redirect_url, $orderDetailTpl, $orderDetailRowTpl, $orderContactsRowTpl);

        case 'notify':
            if($_SERVER['REQUEST_METHOD'] != 'POST') {
                $errorCode = 405;
                $errorMessage = 'Method Not Allowed';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $rawbody = file_get_contents('php://input');

            $data = json_decode($rawbody, true);

            // Check request signature
            if ( !$pk_obj->isAuthorized() ) {
                $errorCode = 400;
                $errorMessage = 'Unauthorized Request';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            if (!isset($_REQUEST['order_id']) || !isset($data['transaction']['status'])) {
                $errorCode = 400;
                $errorMessage = 'Bad Request';
                return $pk_obj->setError($errorCode, $errorMessage);
            }

            $order_id = $_REQUEST['order_id'];

            $order = $modx->getObject('shk_order', $order_id);
            if (!$order) {
                $errorCode = 500;
                $errorMessage = 'No shk_order object found';
                return $pk_obj->setError($errorCode, $errorMessage);
            }


            if ($data['transaction']['status'] == 'successful') {
                $order->set('status', $modx->getOption('statusPaid', $scriptProperties, 6));
            } else {
                if ($order->get('status') != $modx->getOption('statusPaid', $scriptProperties, 6)) {
                    $order->set('status', $modx->getOption('statusCancel', $scriptProperties, 5));
                }
            }
            $order->save();

            return true;

        case 'success':
            $pdo = $modx->getService('pdoTools');

            return $pdo->getChunk($modx->getOption('chunkOrderSuccessPaid', $scriptProperties, 'orderSuccessPaid'));

        default:
            break;
    }

}
