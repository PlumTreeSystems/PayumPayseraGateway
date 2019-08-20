<?php


namespace PTS\Paysera\Tests\Action;


use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Symfony\Security\TokenFactory;
use Payum\Core\Model\Identity;
use Payum\Core\Model\Payment;
use Payum\Core\Model\Token;
use Payum\Core\Request\Convert;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Tests\GenericActionTest;
use PTS\Paysera\Action\ConvertPaymentAction;

class ConvertPaymentActionTest extends GenericActionTest
{
    protected $requestClass = Convert::class;

    protected $actionClass = ConvertPaymentAction::class;

    /**
     * @test
     */
    public function shouldImplementActionInterface()
    {
        $rc = new \ReflectionClass(ConvertPaymentAction::class);
        $this->assertTrue($rc->isSubclassOf(ActionInterface::class));
    }

    public function provideSupportedRequests()
    {
        return array(
            array(new $this->requestClass(new Payment(), 'array')),
            array(new $this->requestClass($this->createMock('Payum\Core\Model\PaymentInterface'), 'array')),
            array(new $this->requestClass(new Payment(), 'array', $this->createMock('Payum\Core\Security\TokenInterface'))),
        );
    }

    public function provideNotSupportedRequests()
    {
        return array(
            array('foo'),
            array(array('foo')),
            array(new \stdClass()),
            array($this->getMockForAbstractClass('Payum\Core\Request\Generic', array(array()))),
            array(new $this->requestClass($this->createMock('Payum\Core\Model\PaymentInterface'), 'notArray')),
        );
    }

    /**
     * @test
     */
    public function shouldImplementGenericTokenFactoryAwareInterface()
    {
        $rc = new \ReflectionClass(ConvertPaymentAction::class);
        $this->assertTrue($rc->isSubclassOf(GenericTokenFactoryAwareInterface::class));
    }

    /*

    public function shouldConvertModelToOrder()
    {
        $tokenFactory = $this->createMock(TokenFactoryInterface::class);
        $tokenFactoryInterface = $this->getMockBuilder('Payum\Core\Security\GenericTokenFactory')
            ->setConstructorArgs([$tokenFactory, ['notify' => 'payum_notify_do']])
            ->getMock();

        $token = new Token();
        $payment = new Payment();

        $token->setTargetUrl('captureUrl');
        $token->setAfterUrl('afterUrl');
        $token->setGatewayName('theGatewayName');

        $token->setDetails($identity = new Identity('1', $payment));

        $payment->setNumber('theNumber');
        $payment->setCurrencyCode('USD');
        $payment->setTotalAmount(123);
        $payment->setDescription('the description');
        $payment->setClientId('theClientId');
        $payment->setClientEmail('theClientEmail');

        $action = new ConvertPaymentAction();

        $action->setGenericTokenFactory($tokenFactoryInterface);

        $convert = new Convert($payment, 'array', $token);

        $action->execute($convert);

        $details = $convert->getResult();

        var_dump($details);

        $this->assertNotEmpty($details);
    }
    */

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        $this->expectNotToPerformAssertions();
        new $this->actionClass();
    }


}