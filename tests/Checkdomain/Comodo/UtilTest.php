<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\Model\Account;
use Checkdomain\Comodo\Util;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a class to simulate Requests, and return reponse String for testing purposes
     *
     * @param $reponseString
     */
    protected function createClientMock($reponseString)
    {
        $client   = $this->getMock('Guzzle\Http\Client');
        $request  = $this->getMock('Guzzle\Http\Message\Request', array(), array(), '', false);
        $response = $this->getMock('Guzzle\Http\Message\Response', array(), array(), '', false);

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($reponseString));

        $request
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($response));

        $client
            ->expects($this->any())
            ->method('post')
            ->will($this->returnValue($request));

        return $client;
    }

    /**
     * @return array|bool
     */
    public function testAutoApplySSL()
    {
        echo "testAutoApplySSL:\n";

        // simulated response Text
        $responseText = "errorcode=0&";
        $responseText .= "domain_name=www.test-domain.org&";
        $responseText .= "whois_email=support@test-domain.org";

        $util    = new Util();
        $account = new Account("test_user", "test_password");

        $util->setAccount($account);
        $util->setClient($this->createClientMock($responseText));

        $params = array(
            "test"                => "Y",
            "product"             => 287,
            "serverSoftware"      => 2,
            "csr"                 => "--certificate--",
            "isCustomerValidated" => "Y",
            "days"                => 365,
        );

        $ret = $util->autoApplySSL($params);

        print_r($ret);

        return $ret;

    }

    /**
     * @return array|bool
     */
    public function testGetDcvMailAddresses()
    {
        echo "testGetDcvMailAddresses:\n";

        // simulated response Text
        $responseText = "0\n";
        $responseText .= "domain_name	www.test-domain.org\n";
        $responseText .= "whois_email	 suport@test-domain.org\n";

        $util    = new Util();
        $account = new Account("test_user", "test_password");

        $util->setAccount($account);
        $util->setClient($this->createClientMock($responseText));

        $params = array(
            "domainName" => "www.test-domain.org"
        );

        $ret = $util->getDcvMailAddresses($params);

        print_r($ret);

        return $ret;
    }

    /**
     * @return array|bool
     */
    public function testResendDcvMail()
    {
        echo "testResendDcvMail:\n";

        // simulated response Text
        $responseText = "errorcode=0";

        $util = new Util();

        $account = new Account("test_user", "test_password");

        $util->setAccount($account);
        $util->setClient($this->createClientMock($responseText));

        $params = array(
            "orderNumber"     => "1234567",
            "dcvEmailAddress" => "test@test-domain.com",
        );

        $ret = $util->resendDcvMail($params);

        print_r($ret);

        return $ret;
    }

    /**
     * @return array|bool
     */
    public function testEnterDcvCode()
    {
        echo "testEnterDcvCode:\n";

        // simulated response Text
        $responseText = "<html><body><p>You have entered the correct Domain Control Validation code. Your certificate will now be issued and emailed to you shortly. Please close this window now.</p></body></html>";

        $util = new Util();
        $util->setClient($this->createClientMock($responseText));

        $params = array(
            "orderNumber" => "1234567",
            "dcvCode"     => "testtesttest",
        );

        $ret = $util->enterDcvCode($params);

        print_r($ret);

        return $ret;
    }

    /**
     * @return array|bool
     */
    public function testAutoRevokeSSL()
    {
        echo "testAutoRevokeSSL:\n";

        $responseText = "errorcode=0";

        $util    = new Util();
        $account = new Account("test_user", "test_password");
        $util->setAccount($account);
        $util->setClient($this->createClientMock($responseText));

        $params = array(
            "orderNumber" => "1234567"
        );

        $ret = $util->autoRevokeSSL($params);

        print_r($ret);

        return $ret;
    }
}