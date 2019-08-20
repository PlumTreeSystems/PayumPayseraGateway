<?php


namespace PTS\Paysera\Tests\Action;


use Payum\Core\GatewayAwareInterface;
use Payum\Core\Model\ArrayObject;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Tests\GenericActionTest;
use PTS\Paysera\Action\NotifyAction;
use PTS\Paysera\Api;

class NotifyActionTest extends GenericActionTest
{
    protected $requestClass = 'Payum\Core\Request\Notify';
    protected $actionClass = NotifyAction::class;

    /**
     * @test
     */
    public function shouldBeSubClassOfGatewayAwareAction()
    {
        $rc = new \ReflectionClass(NotifyAction::class);
        $this->assertTrue($rc->isSubclassOf(GatewayAwareInterface::class));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWithWrongParams()
    {
        $this->expectException(\WebToPayException::class);

        $expectedModel = array('foo' => 'fooVal');

        $apiMock = $this->createMock(Api::class);
        $action = new NotifyAction();
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetHttpRequest::class));
        $apiMock
            ->expects($this->once())
            ->method('doNotify');

        $action->setGateway($gatewayMock);
        $action->setApi($apiMock);

        $action->execute(new Notify($expectedModel));
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