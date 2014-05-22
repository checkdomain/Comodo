<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\CommunicationAdapter;
use Checkdomain\Comodo\ImapHelper;
use Checkdomain\Comodo\ImapWithSearch;
use Checkdomain\Comodo\Model\Account;
use Checkdomain\Comodo\Util;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a class to simulate Requests, and return response String for testing purposes
     *
     * @param $responseString
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createClientMock($responseString)
    {
        $client   = $this->getMock('Guzzle\Http\Client');
        $request  = $this->getMock('Guzzle\Http\Message\Request', array(), array(), '', false);
        $response = $this->getMock('Guzzle\Http\Message\Response', array(), array(), '', false);

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($responseString));

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
     * test for applying SSL
     */
    public function testAutoApplySSL()
    {
        // simulated response Text
        $responseText = "errorcode=1&";
        $responseText .= "totalCost=12.98&";
        $responseText .= "orderNumber=123456789&";
        $responseText .= "certificateID=abc123456&";
        $responseText .= "expectedDeliveryTime=123456&";

        $util = $this->createUtilClass();
        $util->getCommunicationAdapter()->setClient($this->createClientMock($responseText));

        $params = array(
            "test"                => "Y",
            "product"             => 287,
            "serverSoftware"      => 2,
            "csr"                 => ("-----BEGIN CERTIFICATE REQUEST-----base64-----END CERTIFICATE REQUEST-----"),
            "isCustomerValidated" => "Y",
            "showCertificateID"   => "Y",
            "days"                => 365,
        );

        $object = $util->autoApplySSL($params);

        $this->assertInstanceOf('\Checkdomain\Comodo\Model\Result\AutoApplyResult', $object);

        $this->assertEquals("12.98", $object->getTotalCost());
        $this->assertEquals("123456", $object->getExpectedDeliveryTime());
        $this->assertEquals("abc123456", $object->getCertificateID());
        $this->assertEquals("123456789", $object->getOrderNumber());
    }

    /**
     * test for getting dvc mail addresses
     */
    public function testGetDCVEMailAddressList()
    {
        // simulated response Text
        $responseText = "0\n";
        $responseText .= "domain_name	www.test-domain.org\n";
        $responseText .= "whois_email	 support@test-domain.org\n";

        $util = $this->createUtilClass();
        $util->getCommunicationAdapter()->setClient($this->createClientMock($responseText));

        $params = array(
            "domainName" => "www.test-domain.org"
        );

        $object = $util->getDCVEMailAddressList($params);

        $this->assertInstanceOf('\Checkdomain\Comodo\Model\Result\GetDCVEMailAddressListResult', $object);

        $this->assertEquals("support@test-domain.org", $object->getWhoisEmail());
        $this->assertEquals("www.test-domain.org", $object->getDomainName());
    }

    /**
     * test for resending dcv mail
     */
    public function testResendDCVEMail()
    {
        // simulated response Text
        $responseText = "errorcode=0";

        $util = $this->createUtilClass();
        $util->getCommunicationAdapter()->setClient($this->createClientMock($responseText));

        $params = array(
            "orderNumber"     => "1234567",
            "dcvEmailAddress" => "webmaster@tobias-nitsche.de",
        );

        $return = $util->resendDCVEMail($params);

        $this->assertEquals(true, $return);
    }

    /**
     * test for entering dcv code
     */
    public function testEnterDCVCode()
    {
        // simulated response Text
        $responseText = "<html><body><p>You have entered the correct Domain Control Validation code. ";
        $responseText .= "Your certificate will now be issued and emailed to you shortly. ";
        $responseText .= "Please close this window now.";
        $responseText .= "</p></body></html>";

        $util = $this->createUtilClass();
        $util->getCommunicationAdapter()->setClient($this->createClientMock($responseText));

        $params = array(
            "orderNumber" => "1234567",
            "dcvCode"     => "testtesttest",
        );

        $return = $util->enterDcvCode($params);

        $this->assertEquals(true, $return);
    }

    /**
     * test for revoke ssl
     */
    public function testAutoRevokeSSL()
    {
        $responseText = "errorcode=0";

        $util = $this->createUtilClass();
        $util->getCommunicationAdapter()->setClient($this->createClientMock($responseText));

        $params = array(
            "orderNumber" => "1234567"
        );

        $return = $util->autoRevokeSSL($params);

        $this->assertEquals(true, $return);
    }

    public function testMail() {
        $util = $this->createUtilClass();

        print_r($util->getMails("www.tobias-nitsche.de", 14365102));
    }

    /**
     * little helper to create the util class
     *
     * @return Util
     */
    protected function createUtilClass()
    {
        #$imapHelper           = null;
        #$imapWithSearch       = null;

        $imapHelper = new ImapHelper();
        $imapWithSearch = new ImapWithSearch(array(
            'host'     => 'mail.checkdomain.de',
            'ssl'      => 'TLS',
            'user'     => 'cdssl',
            'password' => 'TrooperDoooper717'
        ));

        $communicationAdapter = new CommunicationAdapter(new Account("test_user", "test_password"));

        $util = new Util($communicationAdapter, $imapWithSearch, $imapHelper);

        return $util;
    }
}