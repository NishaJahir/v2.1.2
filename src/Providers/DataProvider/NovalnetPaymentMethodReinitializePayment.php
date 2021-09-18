<?php

namespace Novalnet\Providers\DataProvider;

use Plenty\Plugin\Templates\Twig;
use Novalnet\Helper\PaymentHelper;
use Novalnet\Services\PaymentService;
use Plenty\Plugin\ConfigRepository;

class NovalnetPaymentMethodReinitializePayment
{
  
  public function call(Twig $twig, $arg):string
  {
    /** @var PaymentHelper $paymentHelper */
    $paymentHelper = pluginApp(PaymentHelper::class);
    $paymentService = pluginApp(PaymentService::class);
    $config = pluginApp(ConfigRepository::class);
    $paymentKey = 'NOVALNET_SEPA';
    $paymentHelper->logger('order details', $arg[0]);
    $name = trim($config->get('Novalnet.' . strtolower($paymentKey) . '_payment_name'));
    $paymentName = ($name ? $name : $paymentHelper->getTranslatedText(strtolower($paymentKey)));
    $endUserName = '';
    $endCustomerName = 'Norbert Maier';
    $show_birthday = false;
    
    
    return $twig->render('Novalnet::NovalnetPaymentMethodReinitializePayment', [
      "order" => $arg[0], 
      "paymentMethodId" => 6002,
      'nnPaymentProcessUrl' => $paymentService->getProcessPaymentUrl(),
      'paymentMopKey'     =>  $paymentKey,
      'paymentName' => $paymentName,  
       'endcustomername'=> empty(trim($endUserName)) ? $endCustomerName : $endUserName,
       'nnGuaranteeStatus' => $show_birthday ? $guaranteeStatus : '',
      'reInit' => 1
   ]);
  }
}
