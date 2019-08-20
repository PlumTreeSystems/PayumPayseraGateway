<?php

namespace PTS\Paysera\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;

class ConvertPaymentAction implements ActionInterface, GenericTokenFactoryAwareInterface
{

    use GenericTokenFactoryAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        /**
         * @var $order PaymentInterface
         */
        $order = $request->getSource();

        $details = ArrayObject::ensureArrayObject($order->getDetails());


        $details['amount'] = $order->getTotalAmount();
        $details['currency'] = $order->getCurrencyCode();
        $details['orderid'] = $order->getNumber();
        $details['description'] = $order->getDescription();
        $details['p_email'] = $order->getClientEmail();
        $details['personcode'] = $order->getClientId();
        $details['customerIp'] = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : null;

        $token = $request->getToken();

        $details['accepturl'] = $token->getTargetUrl();
        $details['cancelurl'] = $token->getTargetUrl();

        $notifyToken = $this->tokenFactory->createNotifyToken(
            $token->getGatewayName(),
            $token->getDetails()
        );

        $details['callbackurl'] = $notifyToken->getTargetUrl();

        $request->setResult((array)$details);

    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() == 'array';
    }
}
