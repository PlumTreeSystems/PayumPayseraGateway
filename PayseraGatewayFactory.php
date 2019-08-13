<?php

namespace PlumTreeSystems\Paysera;

use PlumTreeSystems\Paysera\Action\AuthorizeAction;
use PlumTreeSystems\Paysera\Action\CancelAction;
use PlumTreeSystems\Paysera\Action\ConvertPaymentAction;
use PlumTreeSystems\Paysera\Action\CaptureAction;
use PlumTreeSystems\Paysera\Action\NotifyAction;
use PlumTreeSystems\Paysera\Action\RefundAction;
use PlumTreeSystems\Paysera\Action\StatusAction;
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
                'project_id' => '',
                'password' => '',
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'project_id', 'password'
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array)$config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
