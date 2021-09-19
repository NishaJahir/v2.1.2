<?php

namespace Novalnet\Providers\DataProvider;

use Plenty\Plugin\Templates\Twig;
use Novalnet\Helper\PaymentHelper;

class NovalnetPaymentMethodReinitializePaymentScript
{
  public function call(Twig $twig):string
  {
    
    $paymentHelper = pluginApp(PaymentHelper::class);
    
    return $twig->render('Novalnet::NovalnetPaymentMethodReinitializePaymentScript', ['mopIds' => ['paymentMethodId' => 6003]]);
  }
}
