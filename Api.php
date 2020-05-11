<?php
namespace PTS\Paysera;

use function League\Uri\create;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpPostRedirect;
use WebToPay;

class Api
{
    /**
     * @var mixed
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
        return WebToPay::validateAndParseData($fields, $this->options['projectid'], $this->options['sign_password']);
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
