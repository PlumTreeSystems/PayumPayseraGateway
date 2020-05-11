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
            case 'pending':
            case 'NEW':
            $request->markNew();
                break;
            case 'COMPLETED':
                $request->markCaptured();
                break;
            case 'FAILED':
                $request->markFailed();
                break;
            case 'NOT_EXECUTED':
                $request->markPending();
                break;
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
