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
use Zend\Mail\Storage\Message;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ImapAdapter
     */
    protected function createImapAdpater()
    {
        $imapAdapter = new ImapAdapter();

        $imapAdapter->setInstance($this->createImapExtension());

        return $imapAdapter;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createImapExtension()
    {
        $imapExtension = $this->getMockBuilder(ImapExtension::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $imapExtension
            ->expects($this->any())
            ->method('getFolders')
            ->will($this->returnValue(array($this->createZendImapStorageFolder())));

        $imapExtension
            ->expects($this->any())
            ->method('selectFolder')
            ->will($this->returnValue(true));

        return $imapExtension;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createZendImapStorageFolder()
    {
        $folder = $this->getMockBuilder(Folder::class)
            ->setConstructorArgs(['INBOX'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        return $folder;
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
        /**
         * @var ImapHelper $imapHelper
         */
        $imapHelper = $this->createImapHelper();

        /**
         * @var ImapAdapter $imapAdapter
         */
        $imapAdapter = $this->createImapAdpater();

        $communicationAdapter = new CommunicationAdapter(new Account('test_user', 'test_password'));

        if ($client != null) {
            $communicationAdapter->setClient($client);
        }

        $util = new Util($communicationAdapter, $imapAdapter, $imapHelper);

        return $util;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function createImapHelper()
    {
        $imapHelper = $this->getMockBuilder(ImapHelper::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableAutoload()
            ->getMock();

        return $imapHelper;
    }

    /**
     * Creates a class to simulate Requests, and return response String for testing purposes
     *
     * @param $responseString
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
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
