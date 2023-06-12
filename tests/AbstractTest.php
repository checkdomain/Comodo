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
use Laminas\Mail\Storage\Folder;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Checkdomain\Comodo\ImapHelper
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\Checkdomain\Comodo\ImapAdapter
     */
    protected function createImapAdapter()
    {
        $imapAdapter = new ImapAdapter();
        $imapAdapter->setInstance($this->createImapExtension());

        return $imapAdapter;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Checkdomain\Comodo\ImapExtension
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
            ->method('selectFolder')
            ->will($this->returnValue(true));

        return $imapExtension;
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
     * Creates a class to simulate Requests, and return response String for testing purposes
     *
     * @param $responseString
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\GuzzleHttp\Client
     */
    protected function createGuzzleClient($responseString)
    {
        $client   = $this->getMockBuilder(Client::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $body     = $this->getMockBuilder(StreamInterface::class)->getMock();

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
