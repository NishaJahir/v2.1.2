<?php

namespace Novalnet\Providers\DataProvider;

use Plenty\Plugin\Templates\Twig;
use Novalnet\Helper\PaymentHelper;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Method\Models\PaymentMethod;

class NovalnetPaymentMethodReinitializePaymentScript
{
  public function call(Twig $twig, $arg):string
  {
    $order = $arg[0];
    $paymentHelper = pluginApp(PaymentHelper::class);
    $paymentMethodRepository = pluginApp(PaymentMethodRepositoryContract::class);
    $paymentMethods          = $paymentMethodRepository->allForPlugin('plenty_novalnet');
    if(!is_null($paymentMethods))
    {
       $paymentMethodIds              = [];
        foreach ($paymentMethods as $paymentMethod) {
          if ($paymentMethod instanceof PaymentMethod) {
              $paymentMethodIds[] = $paymentMethod->id;
          }
        }
    }
        
    $paymentHelper->logger('ids', $paymentMethodIds);
    
    return $twig->render('Novalnet::NovalnetPaymentMethodReinitializePaymentScript', ['paymentMethodIds' => $paymentMethodIds, 'order' => $order]);
  }
}
