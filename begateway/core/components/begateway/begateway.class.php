<?php

/**
 * Class BegatewayPayment
 */
class BegatewayPayment
{

    public $modx = null;
    private $shopID;
    private $shopSecretKey;
    private $shopPublicKey;
    private $test;
    private $paymentDomain;
    private $success_url;
    private $failure_url;
    private $notify_url;
    private $currencyMode;
    private $currencyDefault;

    /**
     * BegatewayPayment constructor.
     * @param modX $modx
     */
    public function __construct(modX &$modx)
    {
        $this->modx =& $modx;
    }

    /**
     * @param $shopID
     * @param $shopSecretKey
     * @param $test
     * @param $paymentDomain
     * @param $success_url
     * @param $failure_url
     * @param $notify_url
     * @param $currencyMode
     * @param $currencyDefault
     */
    public function setParams($shopID, $shopSecretKey, $shopPublicKey, $test, $paymentDomain, $success_url, $failure_url, $notify_url, $currencyMode, $currencyDefault)
    {
        $this->shopID = $shopID;
        $this->shopSecretKey = $shopSecretKey;
        $this->shopPublicKey = $shopPublicKey;
        $this->test = $test;
        $this->paymentDomain = $paymentDomain;
        $this->success_url = $success_url;
        $this->failure_url = $failure_url;
        $this->notify_url = $notify_url;
        $this->currencyMode = $currencyMode;
        $this->currencyDefault = $currencyDefault;
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getPayLink($order)
    {
        $contacts = $this->getContactInfo($order['contacts']);

        $amount = round($order['price'] * 100);
        $currency = $order['currency'];

        if ($this->currencyMode == 'default') {
            $currency = $this->currencyDefault;
            if ($order['currency'] != $this->currencyDefault && !empty($_SESSION['shk_curr_rate'])) {
                $amount = round($order['price'] * 100 / $_SESSION['shk_curr_rate']);
            }
        }

        $this->success_url = $this->correctUrl($this->success_url, $order['id']);
        $this->failure_url = $this->correctUrl($this->failure_url, $order['id']);
        $this->notify_url = $this->correctUrl($this->notify_url, $order['id']);

        $data = [
            'checkout' => [
                'order' => [
                    'amount' => $amount,
                    'currency' => $currency,
                    'description' => 'Order N: ' . $order['id'],
                    'tracking_id' => $order['id'],
                    'additional_data' => [
                        'meta' => [
                            'platform_data' => 'Shopkeeper3',
                            'integration_data' => 'beGateway payment module v1.02'
                        ],
                    ],
                ],
                'settings' => [
                    'success_url' => $this->success_url,
                    'decline_url' => $this->failure_url,
                    'fail_url' => $this->failure_url,
                    'cancel_url' => $this->failure_url,
                    'notification_url' => $this->notify_url,
                    'language' => $this->modx->getOption('cultureKey'),
                ],
                'customer' => [
                    'email' => $contacts['email'],
                    'first_name' => $contacts['fullname'],
                    'phone' => $contacts['phone'],
                ],
                'transaction_type' => 'payment',
                'version' => 2,
                'test' => $this->test,
            ]
        ];

        return $this->curlSubmit($this->paymentDomain . '/ctp/api/checkouts', $data, $this->shopID, $this->shopSecretKey);
    }

    /**
     * Return Contact info in associative array
     *
     * @param $contacts
     * @return array
     */
    public function getContactInfo($contacts)
    {
        $tmp_arr = array();
        foreach( $contacts as $contact ) {
            $tmp_arr[$contact['name']] = $contact['value'];
        }

        return $tmp_arr;
    }

    /**
     * Get Purchase Items from Order
     *
     * @param $order_id
     * @return array
     */
    public function getPurchases($order_id)
    {
        $output = array();

        $query = $this->modx->newQuery('shk_purchases');
        $query->where(array('order_id' => $order_id));
        $query->sortby('id', 'asc');
        $purchases = $this->modx->getIterator('shk_purchases', $query);

        if ($purchases) {
            foreach ($purchases as $purchase) {
                $p_data = $purchase->toArray();

                if (!empty($p_data['options'])) {
                    $p_data['options'] = json_decode($p_data['options'], true);
                }

                $fields_data = array();
                if(!empty($p_data['data'])) {
                    $fields_data = json_decode($p_data['data'], true);
                    unset($p_data['data']);
                }

                $purchase_data = array_merge($fields_data, $p_data);

                array_push($output, $purchase_data);
            }

        }

        return $output;
    }

    /**
     * @param $resource_id
     * @param null $order_id
     * @return string
     */
    public function correctUrl($resource_id, $order_id = null)
    {
        $parameters = array();

        if ($order_id) {
            $parameters['order_id'] = $order_id;
        }

        $urlCorrectMode = $this->modx->makeUrl($resource_id, '', $parameters, 'full');

        return htmlspecialchars_decode($urlCorrectMode);
    }

    /**
     * @param $host
     * @param $data
     * @param $shopId
     * @param $shopSecretKey
     * @return mixed
     */
    public function curlSubmit($host, $data, $shopId, $shopSecretKey)
    {
        $process = curl_init($host);
        $json = json_encode($data);

        if (!empty($data)) {
            curl_setopt($process, CURLOPT_HTTPHEADER,
                array(
                    'Accept: application/json',
                    'Content-type: application/json',
                    'X-API-Version: 2',
                )
            );
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, $json);
        } else {
            curl_setopt($process, CURLOPT_HTTPHEADER,
                array(
                    'Accept: application/json',
                    'X-API-Version: 2',
                ),
        );
        }

        curl_setopt($process, CURLOPT_URL, $host);
        curl_setopt($process, CURLOPT_USERPWD, $shopId . ":" . $shopSecretKey);
        curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, TRUE);
        $response = curl_exec($process);
        curl_close($process);

        return json_decode($response);
    }

    /**
     * Render Invoice Chunk
     *
     * @param $order
     * @param $orderStatus
     * @param $redirect_url
     * @param $orderDetailTpl
     * @param $orderDetailRowTpl
     * @param $orderContactsRowTpl
     * @return mixed
     */
    public function renderInvoice($order, $orderStatus, $redirect_url, $orderDetailTpl, $orderDetailRowTpl, $orderContactsRowTpl)
    {
        $pdo = $this->modx->getService('pdoTools');

        //purchases
        $purchasesOutput = '';
        $purchases = $this->getPurchases($order['id']);
        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                if (empty($purchase['addit_data'])) {
                    $purchase['addit_data'] = '&mdash;';
                }
                $purchasesOutput .= $pdo->getChunk($orderDetailRowTpl, $purchase);
            }
        }
        //contacts
        $contactsOutput = '';
        if (!empty($order['contacts'])) {
            foreach ($order['contacts'] as $contact) {
                if (empty($contact['value'])) {
                    $contact['value'] = '&mdash;';
                }
                $contactsOutput .= $pdo->getChunk($orderContactsRowTpl, $contact);
            }
        }

        $order_data = [
            'order_id' => $order['id'],
            'redirect_url' => $redirect_url,
            'purchases' => $purchasesOutput,
            'delivery_name' => $order['delivery'],
            'delivery_price' => $order['delivery_price'],
            'price_total' => $order['price'],
            'currency' => $order['currency'],
            'contacts' => $contactsOutput,
            'orderStatus' => $orderStatus,
        ];

        return $pdo->getChunk($orderDetailTpl, $order_data);
    }

    /**
     * Set Errors params and header for error page
     *
     * @param $code
     * @param $message
     * @return string
     */
    public function setError($code, $message)
    {
        $this->modx->log(MODX_LOG_LEVEL_ERROR, $code . ": " . $message);
        header("{$_SERVER['SERVER_PROTOCOL']} " . $code . " " . $message);
        return '<h1>Error ' . $code . '</h1><p>' . $message . '</p>';
    }

}
