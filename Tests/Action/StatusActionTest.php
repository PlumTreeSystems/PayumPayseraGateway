<?php


namespace PTS\Paysera\Tests\Action;


use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Tests\GenericActionTest;
use PTS\Paysera\Action\StatusAction;

class StatusActionTest extends GenericActionTest
{

    protected $requestClass = 'Payum\Core\Request\GetHumanStatus';
    protected $actionClass = StatusAction::class;

    /**
     * @test
     */
    public function shouldBeSubClassOfGatewayAwareAction()
    {
        $rc = new \ReflectionClass(StatusAction::class);
        $this->assertTrue($rc->implementsInterface('Payum\Core\Action\ActionInterface'));
    }

    /**
     * @test
     */
    public function shouldMarkAsNew()
    {
        $request = new GetHumanStatus(array(
            'status' => 'NEW'
        ));
        $action = new StatusAction();
        $action->execute($request);
        $this->assertTrue($request->isNew(), 'Request should be marked as new');
    }

    /**
     * @test
     */
    public function shouldMarkAsPending()
    {
        $request = new GetHumanStatus(array(
            'status' => 'PENDING'
        ));
        $action = new StatusAction();
        $action->execute($request);
        $this->assertTrue($request->isPending(), 'Request should be marked as pending');
    }

    /**
     * @test
     */
    public function shouldMarkAsCaptured()
    {
        $request = new GetHumanStatus(array(
            'status' => 'COMPLETED'
        ));
        $action = new StatusAction();
        $action->execute($request);
        $this->assertTrue($request->isCaptured(), 'Request should be marked as captured');
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        $this->expectNotToPerformAssertions();
        new $this->actionClass();
    }
}