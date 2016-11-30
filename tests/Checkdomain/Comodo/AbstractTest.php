<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\CommunicationAdapter;
use Checkdomain\Comodo\ImapExtension;
use Checkdomain\Comodo\ImapHelper;
use Checkdomain\Comodo\ImapAdapter;
use Checkdomain\Comodo\Model\Account;
use Checkdomain\Comodo\Util;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Mail\Storage\Folder;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Checkdomain\Comodo\ImapAdapter
     */
    protected function createImapAdapter()
    {
        $imapAdapter = new ImapAdapter();
        $imapAdapter->setInstance($this->createImapExtension());

        return $imapAdapter;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Checkdomain\Comodo\ImapExtension
     */
    protected function createImapExtension()
    {
        $imapExtension = $this
            ->getMockBuilder(ImapExtension::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $imapExtension
            ->expects($this->any())
            ->method('getFolders')
            ->will($this->returnValue([$this->createZendImapStorageFolder()]));

        $imapExtension
            ->expects($this->any())
            ->method('selectFolder')
            ->will($this->returnValue(true));

        return $imapExtension;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Mail\Storage\Folder
     */
    protected function createZendImapStorageFolder()
    {
        return $this
            ->getMockBuilder(Folder::class)
            ->setConstructorArgs(['INBOX'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * little helper to create the util class
     *
     * @param Client $client
     *
     * @return Util
     */
    protected function createUtil(Client $client = null)
    {
        $imapHelper = $this->createImapHelper();
        $imapAdapter = $this->createImapAdapter();
        $communicationAdapter = new CommunicationAdapter(new Account('test_user', 'test_password'));

        if ($client != null) {
            $communicationAdapter->setClient($client);
        }

        return new Util($communicationAdapter, $imapAdapter, $imapHelper);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Checkdomain\Comodo\ImapHelper
     */
    public function createImapHelper()
    {
        return $this
            ->getMockBuilder(ImapHelper::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableAutoload()
            ->getMock();
    }

    /**
     * Creates a class to simulate Requests, and return response String for testing purposes
     *
     * @param $responseString
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\GuzzleHttp\Client
     */
    protected function createGuzzleClient($responseString)
    {
        $client   = $this->createMock(Client::class);
        $response = $this->createMock(ResponseInterface::class);
        $body     = $this->createMock(StreamInterface::class);

        $body
            ->expects($this->any())
            ->method('getContents')
            ->will($this->returnValue($responseString));

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));

        $client
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue($response));

        return $client;
    }
}
