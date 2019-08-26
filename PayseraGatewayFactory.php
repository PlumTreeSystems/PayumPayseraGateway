<?php

namespace PTS\Paysera;

use PTS\Paysera\Action\AuthorizeAction;
use PTS\Paysera\Action\CancelAction;
use PTS\Paysera\Action\ConvertPaymentAction;
use PTS\Paysera\Action\CaptureAction;
use PTS\Paysera\Action\NotifyAction;
use PTS\Paysera\Action\RefundAction;
use PTS\Paysera\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class PayseraGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'paysera',
            'payum.factory_title' => 'paysera',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'projectid' => '',
                'sign_password' => '',
                'test' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'projectid', 'sign_password'
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array)$config);
            };
        }
    }
}
