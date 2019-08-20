<?php

namespace PTS\Paysera\Tests\Action;

use Payum\Core\ApiAwareInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Tests\GenericActionTest;
use PTS\Paysera\Action\CaptureAction;
use PTS\Paysera\Api;

class CaptureActionTest extends GenericActionTest
{
    protected $requestClass = Capture::class;

    protected $actionClass = CaptureAction::class;

    /**
     * @test
     */
    public function shouldImplementsApiAwareInterface()
    {
        $rc = new \ReflectionClass(CaptureAction::class);
        $this->assertTrue($rc->isSubclassOf(ApiAwareInterface::class));
    }

    /** @test */
    public function shouldImplementsGatewayAwareInterface()
    {
        $rc = new \ReflectionClass(CaptureAction::class);
        $this->assertTrue($rc->isSubclassOf(GatewayAwareInterface::class));
    }

    /**
     * @test
     */
    public function shouldDoNothingIfPaymentHasErrorCode()
    {
        $model = [
            'error_code' => '0',
        ];
        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->never())
            ->method('execute');
        $action = new CaptureAction();
        $action->setGateway($gatewayMock);
        $action->execute(new Capture($model));
    }

    /**
     * @test
     */
    public function shouldCallDoPayment()
    {
        $apiMock = $this->createMock(Api::class);
        $action = new CaptureAction();
        $gateway = $this->createGatewayMock();
        $apiMock
            ->expects($this->once())
            ->method('doPayment')
        ;
        $action->setGateway($gateway);
        $action->setApi($apiMock);
        $action->execute(new Capture([]));
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