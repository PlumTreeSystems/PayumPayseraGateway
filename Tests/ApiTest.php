<?php


namespace PTS\Paysera\Tests;

use Payum\Core\Reply\HttpPostRedirect;
use WebToPayException;
use PHPUnit\Framework\TestCase;
use PTS\Paysera\Api;

class ApiTest extends TestCase
{

    public function testApiWhenPayingWithoutCorrectDataThrowsException()
    {
        $this->expectException(\WebToPay_Exception_Validation::class);
        $options = [
            'projectid' => 'testProjectId',
            'sign_password' => 'testSignPassword',
            'test' => false
        ];
        $api = new Api($options);
        $fields = [];
        $api->doPayment($fields);
    }

    /**
     * @test
     **/
    public function testApiWhenPayingWithCorrectDataThrowsRedirect()
    {
        $this->expectException(HttpPostRedirect::class);

        $options = [
            'projectid' => 'testProjectId',
            'sign_password' => 'testSignPassword',
            'test' => false
        ];
        $api = new Api($options);
        $fields = [
            'orderid' => '15',
            'accepturl' => 'testurl',
            'cancelurl' => 'testurl',
            'callbackurl' => 'testurl',
        ];
        $api->doPayment($fields);
    }

    /**
     * @test
     **/
    public function testApiWhenNotifyingWithoutCorrectDataThrowsException()
    {
        $this->expectException(WebToPayException::class);

        $options = [
            'projectid' => 'testProjectId',
            'password' => 'testSignPassword',
            'test' => false
        ];
        $api = new Api($options);
        $fields = [
            'data' => '15',
            'ss1' => 'testurl',
            'ss2' => 'testurl'
        ];
        $api->doNotify($fields);
    }

}