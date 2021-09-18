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
    
    foreach($order->properties as $property) {
      if($property->typeId == 3 )
      {
          $mopId = $property->value;
          break;
      }
    }
    
    $paymentKey = $paymentHelper->getPaymentKeyByMop($mopId);
    $paymentHelper->logger('payment key', $paymentKey);
    
    if (in_array($paymentKey, ['NOVALNET_INVOICE', 'NOVALNET_PREPAYMENT', 'NOVALNET_CASHPAYMENT'])) {
       $serverRequestData = $paymentService->getRequestParameters($basketRepository->load(), $paymentKey);
       $sessionStorage->getPlugin()->setValue('nnPaymentData', $serverRequestData);
       $sessionStorage->getPlugin()->setValue('nnOrderNo',$event->getOrderId());
       $sessionStorage->getPlugin()->setValue('mop',$event->getMop());
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
