<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\CommunicationAdapter;
use Checkdomain\Comodo\ImapHelper;
use Checkdomain\Comodo\ImapWithSearch;
use Checkdomain\Comodo\Model\Account;
use Checkdomain\Comodo\Util;
use Guzzle\Http\Client;
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
}

