<?php
namespace PlumTreeSystems\Paysera;

use Http\Message\MessageFactory;
use function League\Uri\create;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Symfony\Component\HttpFoundation\Request;
use WebToPay;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }


    protected function doRequest($method, array $fields)
    {
        $headers = [];

        $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint(), $headers, http_build_query($fields));

        $response = $this->client->send($request);

        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 200) {
            throw HttpException::factory($request, $response);
        }

        return $response;
    }

    public function doPayment(array $fields)
    {
        $fields['projectid'] = $this->options['project_id'];
        $fields['sign_password'] = $this->options['password'];
        $this->options['sandbox'] ? $fields['test'] = 1 : $fields['test'] = 0;
        $authorizeTokenUrl = $this->getApiEndpoint();
        $data = WebToPay::buildRequest($fields);
        throw new HttpPostRedirect($authorizeTokenUrl, $data);
    }

    public function doNotify(array $fields)
    {
        $response = WebToPay::validateAndParseData(
            $fields,
            $this->options['project_id'],
            $this->options['password']);
        if ($response['status'] === '1') {
            return true;
        }
    }

    /**
     * @return string
     */
    protected function getApiEndpoint()
    {
        return $this->options['sandbox'] ? WebToPay::PAY_URL : WebToPay::PAY_URL;
    }

    public function getApiOptions()
    {
        return $this->options;
    }
}
