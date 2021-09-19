<?php

namespace Novalnet\Providers\DataProvider;

use Plenty\Plugin\Templates\Twig;
use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\PaymentService;
use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;

class NovalnetPaymentMethodReinitializePayment
{
  
  public function call(Twig $twig, $arg):string
  {
    $order = $arg[0];
    $paymentHelper = pluginApp(PaymentHelper::class);
    $paymentService = pluginApp(PaymentService::class);
    $config = pluginApp(ConfigRepository::class);
    $basketRepository = pluginApp(BasketRepositoryContract::class);
    $paymentRepository = pluginApp(PaymentRepositoryContract::class);
    $sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);
    $payments = $paymentRepositoryContract->getPaymentsByOrderId($order['id']);
    $paymentHelper->logger('order', $order);
    $paymentHelper->logger('payment12345', $payments);
    
    foreach($order['properties'] as $property) {
        if($property['typeId'] == 3)
        {
            $mopId = $property['value'];
        }
        if($property['typeId'] == 4)
        {
            $paidStatus = $property['value'];
        }
    }
    
    $paymentKey = $paymentHelper->getPaymentKeyByMop($mopId);
   
       $serverRequestData = $paymentService->getRequestParameters($basketRepository->load(), $paymentKey);
       $paymentHelper->logger('request data', $serverRequestData);
       $sessionStorage->getPlugin()->setValue('nnPaymentData', $serverRequestData);
       $sessionStorage->getPlugin()->setValue('nnOrderNo',$order['id']);
       $sessionStorage->getPlugin()->setValue('mop',$mopId);
       $sessionStorage->getPlugin()->setValue('paymentKey',$paymentKey);
       
       return $twig->render('Novalnet::NovalnetPaymentMethodReinitializePayment', [
        "order" => $order, 
        "paymentMethodId" => $mopId,
        "paymentKey" => $paymentKey
      ]);
     
  }
}
