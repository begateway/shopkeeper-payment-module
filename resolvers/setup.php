<?php

$modx= new modX();
$modx->initialize('mgr');

$resource = $modx->getObject('modResource', ['alias' => 'begateway-pay']);
if (empty($resource)){
    $resource = $modx->newObject('modResource');
    $resource->set('template', 1);                                      // Назначаем ему нужный шаблон
    $resource->set('isfolder', 0);                                      // Указываем, что это не контейнер
    $resource->set('published', 1);
    $resource->set('hidemenu', 1);
    $resource->set('pagetitle', 'Оплатить заказ');                      // Заголовок
    $resource->set('alias', 'begateway-pay');                           // Псевдоним
    $resource->setContent('[[!begateway? action=`payment`]]');          // Содержимое
    $resource->save();                                                  // Сохраняем
}

$resource = $modx->getObject('modResource', ['alias' => 'begateway-success']);
if (empty($resource)){
    $resource = $modx->newObject('modResource');
    $resource->set('template', 1);
    $resource->set('isfolder', 0);
    $resource->set('published', 1);
    $resource->set('hidemenu', 1);
    $resource->set('pagetitle', 'Спасибо за заказ');
    $resource->set('alias', 'begateway-success');
    $resource->setContent('[[!begateway? action=`success`]]');
    $resource->save();
}

$resource = $modx->getObject('modResource', ['alias' => 'begateway-fail']);
if (empty($resource)){
    $resource = $modx->newObject('modResource');
    $resource->set('template', 1);
    $resource->set('isfolder', 0);
    $resource->set('published', 1);
    $resource->set('hidemenu', 1);
    $resource->set('pagetitle', 'Ошибка оплаты');
    $resource->set('alias', 'begateway-fail');
    $resource->setContent('[[!begateway? action=`fail`]]');
    $resource->save();
}

$resource = $modx->getObject('modResource', ['alias' => 'begateway-notify']);
if (empty($resource)){
    $resource = $modx->newObject('modResource');
    $resource->set('template', 0);
    $resource->set('isfolder', 0);
    $resource->set('published', 1);
    $resource->set('hidemenu', 1);
    $resource->set('pagetitle', 'Begateway Notify');
    $resource->set('alias', 'begateway-notify');
    $resource->setContent('[[!begateway? action=`notify`]]');
    $resource->save();
}
