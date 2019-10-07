<?php
namespace PTS\Paysera\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use WebToPay;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (null === $model['status'] || 'NEW' == $model['status'] || 'pending' == $model['status']) {
            $request->markNew();
            return;
        } elseif ($model['status'] == 'COMPLETED') {
            $request->markCaptured();
            return;
        } elseif ($model['status'] == 'FAILED') {
            $request->markFailed();
            return;
        }
        $request->markUnknown();

    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
