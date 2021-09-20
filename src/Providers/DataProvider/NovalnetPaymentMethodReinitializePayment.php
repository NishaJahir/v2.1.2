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
    $payments = $paymentRepository->getPaymentsByOrderId($order['id']);
    $paymentHelper->logger('order', $order);
    $paymentHelper->logger('payment12345', $payments);
    
    // Get payment method Id and status
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
    
     // Get transaction status
    foreach($payments as $payment)
    {
        $properties = $payment->properties;
        foreach($properties as $property)
        {
          if ($property->typeId == 30)
          {
          $tid_status = $property->value;
          }
        }
    }
      
      // Changed payment method key
       $paymentKey = $paymentHelper->getPaymentKeyByMop($mopId);
      // Get the orderamount from order object if the basket amount is empty
       $orderAmount = $paymentHelper->ConvertAmountToSmallerUnit($order['amounts'][0]['invoiceTotal']);
      // Form the payment request data 
       $serverRequestData = $paymentService->getRequestParameters($basketRepository->load(), $paymentKey, false, $orderAmount);
       $sessionStorage->getPlugin()->setValue('nnOrderNo',$order['id']);
       $sessionStorage->getPlugin()->setValue('mop',$mopId);
       $sessionStorage->getPlugin()->setValue('paymentKey',$paymentKey);
       
      if ($paymentService->isRedirectPayment($paymentKey)) {
         $sessionStorage->getPlugin()->setValue('nnPaymentData', $serverRequestData['data']);
      } else {
          $sessionStorage->getPlugin()->setValue('nnPaymentData', $serverRequestData);
      }
       
       if( !in_array($tid_status, [75, 85, 86, 90, 91, 98, 99, 100]) ) {
          return $twig->render('Novalnet::NovalnetPaymentMethodReinitializePayment', [
            'order' => $order, 
            'paymentMethodId' => $mopId,
            'paymentKey' => $paymentKey,
            'isRedirectPayment' => $paymentService->isRedirectPayment($paymentKey),
            'redirectUrl' => $paymentService->getRedirectPaymentUrl(),
          ]);
       } else {
          return '';
       }
  }
}
