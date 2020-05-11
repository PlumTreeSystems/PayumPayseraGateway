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

        switch ($model['status']) {
            case null:
            case 'NEW':
            case 'pending':
                $request->markNew();
            case 'COMPLETED':
                $request->markCaptured();
            case 'FAILED':
                $request->markFailed();
            case 'NOT_EXECUTED':
                $request->markPending();

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
