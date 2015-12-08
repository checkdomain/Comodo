<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\CommunicationAdapter;
use Checkdomain\Comodo\ImapHelper;
use Checkdomain\Comodo\ImapWithSearch;
use Checkdomain\Comodo\Model\Account;
use Checkdomain\Comodo\Util;
use GuzzleHttp\Client;
use Zend\Mail\Storage\Folder;
use Zend\Mail\Storage\Message;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createImapWithSearch()
    {
        $imapWithSearch = $this->getMock('Checkdomain\Comodo\ImapWithSearch', array(), array(), '', false, false);

        $imapWithSearch
            ->expects($this->any())
            ->method('getFolders')
            ->will($this->returnValue(array($this->createZendImapStorageFolder())));

        $imapWithSearch
            ->expects($this->any())
            ->method('selectFolder')
            ->will($this->returnValue(true));

        return $imapWithSearch;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createZendImapStorageFolder()
    {
        $folder = $this->getMock('Zend\Mail\Storage\Folder', array(), array('INBOX'), '', false, false);

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
         * @var ImapWithSearch $imapWithSearch
         */
        $imapWithSearch = $this->createImapWithSearch();

        $communicationAdapter = new CommunicationAdapter(new Account('test_user', 'test_password'));

        if ($client != null) {
            $communicationAdapter->setClient($client);
        }

        $util = new Util($communicationAdapter, $imapWithSearch, $imapHelper);

        return $util;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function createImapHelper()
    {
        $imapHelper = $this->getMock('Checkdomain\Comodo\ImapHelper', null, array(), '', false, false, false);

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
        $client   = $this->getMock('GuzzleHttp\Client');
        $response = $this->getMock('Psr\Http\Message\ResponseInterface');
        $body     = $this->getMock('Psr\Http\Message\StreamInterface');

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
