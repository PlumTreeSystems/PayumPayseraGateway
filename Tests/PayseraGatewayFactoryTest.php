<?php


namespace PTS\Paysera\Tests;

use Payum\Core\Exception\LogicException;
use Payum\Core\Gateway;
use Payum\Core\GatewayFactoryInterface;
use PHPUnit\Framework\TestCase;
use PTS\Paysera\PayseraGatewayFactory;

class PayseraGatewayFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldImplementGatewayFactoryInterface()
    {
        $rc = new \ReflectionClass(PayseraGatewayFactory::class);
        $this->assertTrue($rc->implementsInterface(GatewayFactoryInterface::class));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        $this->expectNotToPerformAssertions();
        new PayseraGatewayFactory();
    }


    /**
     * @test
     */
    public function shouldUseCoreGatewayFactoryPassedAsSecondArgument()
    {
        $coreGatewayFactory = $this->createMock('Payum\Core\GatewayFactoryInterface');
        $factory = new PayseraGatewayFactory([], $coreGatewayFactory);
        $this->assertAttributeSame($coreGatewayFactory, 'coreGatewayFactory', $factory);
    }

    /**
     * @test
     */
    public function shouldAllowCreateGateway()
    {
        $factory = new PayseraGatewayFactory();
        $gateway = $factory->create([
            'projectid' => 'testProjectId',
            'sign_password' => 'testSignPassword'
        ]);
        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertAttributeNotEmpty('apis', $gateway);
        $this->assertAttributeNotEmpty('actions', $gateway);
        $extensions = $this->readAttribute($gateway, 'extensions');
        $this->assertAttributeNotEmpty('extensions', $extensions);
    }

    /**
     * @test
     */
    public function shouldAllowCreateGatewayWithCustomApi()
    {
        $factory = new PayseraGatewayFactory();
        $gateway = $factory->create(['payum.api' => new \stdClass()]);
        $this->assertInstanceOf(Gateway::class, $gateway);
        $this->assertAttributeNotEmpty('apis', $gateway);
        $this->assertAttributeNotEmpty('actions', $gateway);
        $extensions = $this->readAttribute($gateway, 'extensions');
        $this->assertAttributeNotEmpty('extensions', $extensions);
    }

    /**
     * @test
     */
    public function shouldAllowCreateGatewayConfig()
    {
        $factory = new PayseraGatewayFactory();
        $config = $factory->createConfig();
        $this->assertInternalType('array', $config);
        $this->assertNotEmpty($config);
    }

    /**
     * @test
     */
    public function shouldAddDefaultConfigPassedInConstructorWhileCreatingGatewayConfig()
    {
        $factory = new PayseraGatewayFactory(array(
            'foo' => 'fooVal',
            'bar' => 'barVal',
        ));
        $config = $factory->createConfig();
        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('foo', $config);
        $this->assertEquals('fooVal', $config['foo']);
        $this->assertArrayHasKey('bar', $config);
        $this->assertEquals('barVal', $config['bar']);
    }

    /**
     * @test
     */
    public function shouldConfigContainDefaultOptions()
    {
        $factory = new PayseraGatewayFactory();
        $config = $factory->createConfig();
        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('payum.default_options', $config);
        $this->assertArraySubset(
            [
                'projectid' => '',
                'sign_password' => '',
                'test' => true
            ],
            $config['payum.default_options']
        );
    }

    /**
     * @test
     */
    public function shouldConfigContainFactoryNameAndTitle()
    {
        $factory = new PayseraGatewayFactory();
        $config = $factory->createConfig();
        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('payum.factory_name', $config);
        $this->assertEquals('paysera', $config['payum.factory_name']);
        $this->assertArrayHasKey('payum.factory_title', $config);
        $this->assertEquals('paysera', $config['payum.factory_title']);
    }

    /**
     * @test
     */
    public function shouldThrowIfRequiredOptionsNotPassed()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The projectid, sign_password fields are required.');
        $factory = new PayseraGatewayFactory();
        $factory->create();
    }

    /**
     * @test
     */
    public function shouldConfigurePaths()
    {
        $factory = new PayseraGatewayFactory();
        $config = $factory->createConfig();
        $this->assertInternalType('array', $config);
        $this->assertNotEmpty($config);
        $this->assertInternalType('array', $config['payum.paths']);
        $this->assertNotEmpty($config['payum.paths']);
        $this->assertArrayHasKey('PayumCore', $config['payum.paths']);
        $this->assertStringEndsWith('Resources/views', $config['payum.paths']['PayumCore']);
        $this->assertTrue(file_exists($config['payum.paths']['PayumCore']));
    }
}