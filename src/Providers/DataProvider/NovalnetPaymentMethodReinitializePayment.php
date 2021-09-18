<?php

namespace Novalnet\Providers\DataProvider;

use Plenty\Plugin\Templates\Twig;
use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\PaymentService;
use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;

class NovalnetPaymentMethodReinitializePayment
{
  
  public function call(Twig $twig, $arg):string
  {
    $order = $arg[0];
    $paymentHelper = pluginApp(PaymentHelper::class);
    $paymentService = pluginApp(PaymentService::class);
    $config = pluginApp(ConfigRepository::class);
    $basketRepository = pluginApp(BasketRepositoryContract::class);
    $sessionStorage = pluginApp(FrontendSessionStorageFactoryContract::class);
    $paymentHelper->logger('order', $order);
    foreach($order->properties as $properties) {
      foreach($properties as $property) {
        if($property->typeId == 3 )
        {
            $mopId = $property->value;
        }
      }
    }
    
    $paymentKey = $paymentHelper->getPaymentKeyByMop($mopId);
    $paymentHelper->logger('payment keyyy', $paymentKey);
    
       
       $serverRequestData = $paymentService->getRequestParameters($basketRepository->load(), $paymentKey);
       $paymentHelper->logger('request data', $serverRequestData);
       $sessionStorage->getPlugin()->setValue('nnPaymentData', $serverRequestData);
       $sessionStorage->getPlugin()->setValue('nnOrderNo',$order['id']);
       $sessionStorage->getPlugin()->setValue('mop',$mopId);
       $sessionStorage->getPlugin()->setValue('paymentKey',$paymentKey);
       
     if ($paymentKey == 'NOVALNET_INVOICE') {
         $paymentService->paymentCalltoNovalnetServer();
            $paymentService->validateResponse();
     } else {
      return $twig->render('Novalnet::NovalnetPaymentMethodReinitializePayment', [
        "order" => $order, 
        "paymentMethodId" => $mopId,
        "paymentKey" => $paymentKey
      ]);
     }
  }
}
