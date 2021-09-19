<?php

namespace Novalnet\Providers\DataProvider;

use Plenty\Plugin\Templates\Twig;
use Novalnet\Helper\PaymentHelper;

class NovalnetPaymentMethodReinitializePaymentScript
{
  public function call(Twig $twig, $arg):string
  {
    $order = $arg[0];
    $paymentHelper = pluginApp(PaymentHelper::class);
    
    foreach($order['properties'] as $property) {
        if($property['typeId'] == 3)
        {
            $mopId = $property['value'];
        }
    }
    $paymentHelper->logger('script', $mopId);
    return $twig->render('Novalnet::NovalnetPaymentMethodReinitializePaymentScript', ['mopIds' => ['paymentMethodId' => $mopId]]);
  }
}
