<?php

namespace PTS\Paysera\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use PTS\Paysera\Api;
use WebToPay;

class CaptureAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    use ApiAwareTrait;


    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * @param mixed $request
     * @throws \WebToPayException
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (isset($model['error_code']) || $model['status'] === 'COMPLETED') {
            return;
        }

        $httpRequest = new GetHttpRequest();

        $this->gateway->execute($httpRequest);

        if (isset($httpRequest->query['ss1']) && isset($httpRequest->query['ss2'])) {
            return;
        } else {
            $model['status'] = 'NEW';
            $this->api->doPayment((array)$model);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
