<?php
namespace PTS\Paysera;

use Http\Message\MessageFactory;
use function League\Uri\create;
use Payum\Core\Bridge\Spl\ArrayObject;
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
     * @var array
     */
    protected $options = [];

    /**
     * Api constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);

        $this->options = $options;
    }

    public function doPayment(array $fields)
    {
        $fields['projectid'] = $this->options['projectid'];
        $fields['sign_password'] = $this->options['sign_password'];
        $this->options['test'] ? $fields['test'] = 1 : $fields['test'] = 0;
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
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    protected function getApiEndpoint()
    {
        return WebToPay::PAY_URL;
    }

    public function getApiOptions()
    {
        return $this->options;
    }
}
