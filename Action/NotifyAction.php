<?php

namespace PTS\Paysera\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use PTS\Paysera\Api;
use PTS\Paysera\MockedApi;

class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use ApiAwareTrait;

    use GatewayAwareTrait;

    public function __construct($mocked = false)
    {
        $mocked ? $this->apiClass = MockedApi::class : $this->apiClass = Api::class;
    }

    /**
     * @param mixed $request
     * @throws \WebToPayException
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        $response = $this->api->doNotify($httpRequest->method === 'POST' ? $httpRequest->request : $httpRequest->query);

        if (!$response) {
            throw new \WebToPayException('Wrong parameters');
        }

        switch ($response['status']) {
            case '0':
                $model['status'] = 'FAILED';
                break;
            case '1':
                $model['status'] = 'COMPLETED';
                break;
            case '2':
                $model['status'] = 'NOT_EXECUTED';
                break;
        }
        throw new HttpResponse('OK');
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
