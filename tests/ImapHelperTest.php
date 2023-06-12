<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\ImapExtension;
use Checkdomain\Comodo\ImapHelper;
use Laminas\Mail\Storage\Message;

/**
 * Class ImapHelperTest
 */
class ImapHelperTest extends AbstractTest
{
    /**
     * @var ImapHelper
     */
    private $imapHelper;

    /**
     * @var array
     */
    private $messages = [];

    /**
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->imapHelper = new ImapHelper();
        $this->messages = json_decode(file_get_contents(__DIR__.'/data/messages/messages.json'), true);
    }


    /**
     * @param int $messageId
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Laminas\Mail\Storage\Message
     */
    public function createImapStorageMessage($messageId)
    {
        $messageData = $this->messages[$messageId];

        $message = $this
            ->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $message
            ->expects($this->any())
            ->method('getHeader')
            ->will($this->returnCallback(function ($index, $type) use ($messageData) {
                if ($type != 'string') {
                    return null;
                }

                if ($index == 'subject') {
                    return utf8_encode($messageData[$index]);
                }

                return $messageData[$index];
            }));

        $message
            ->expects($this->any())
            ->method('isMultipart')
            ->will($this->returnValue(false));

        $message
            ->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($this->messages[$messageId]['plainText']));

        $message
            ->expects($this->any())
            ->method('getHeaderField')
            ->will($this->returnValue('text/plain'))
            ->with($this->equalTo('Content-Type'));

        return $message;
    }

    /**
     * This test is only for validating correct decoding of the message (subject/plaintext/received)
     */
    public function testMailParse()
    {
        $imapAdapter = $this->createImapAdapter();

        /** @var \PHPUnit_Framework_MockObject_MockObject|ImapExtension $imapExtension */
        $imapExtension = $imapAdapter->getInstance();

        $imapExtension
            ->expects($this->any())
            ->method('search')
            ->will($this->returnValue([0]));

        $imapExtension
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($this->createImapRawMessage(
                base64_decode(file_get_contents(__DIR__.'/data/messages/raw/multipart'))
            )));

        $messages = $this->imapHelper->fetchMails($imapAdapter, null, true, true);
        $message = $messages[0];

        $this->assertEquals('ORDER #12345678 - CONFIRMATION', $message['subject']);
        $this->assertEquals(1404914391, $message['received']);
        $this->assertEquals('test'.chr(10).chr(10), $message['plainText']);
    }

    /**
     * This test is only for validating correct decoding of the attachments
     */
    public function testMailAttachmentParse()
    {
        $imapAdapter = $this->createImapAdapter();

        /** @var \PHPUnit_Framework_MockObject_MockObject|ImapExtension $imapExtension */
        $imapExtension = $imapAdapter->getInstance();

        $imapExtension
            ->expects($this->any())
            ->method('search')
            ->will($this->returnValue([0]));

        $imapExtension
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($this->createImapRawMessage(
                base64_decode(file_get_contents(__DIR__.'/data/messages/raw/with-attachment'))
            )));

        $messages = $this->imapHelper->fetchMails($imapAdapter, null, true, true);
        $attachment = $messages[0]['attachments'][0];

        $this->assertEquals('application/x-zip-compressed', $attachment['mime']);
        $this->assertEquals('finaltest_tobias-nitsche_de.zip', $attachment['filename']);
        $this->assertEquals(3962, strlen($attachment['content']));
    }

    /**
     * test for assuming domain names and ordernumbersof email
     */
    public function testMailAssume()
    {
        $asserts = array_map(function ($message) {
            static $i = 0;

            return [
                'id'          => $i++,
                'folder'      => 'INBOX',
                'subject'     => $message['subject'],
                'received'    => strtotime($message['date']),
                'plainText'   => $message['plainText'],
                'attachments' => null,
                'type'        => $message['type'],
                'domainName'  => $message['domainName'],
                'orderNumber' => $message['orderNumber'],
            ];
        }, $this->messages);

        $imapAdapter = $this->createImapAdapter();

        $imapAdapter
            ->expects($this->any())
            ->method('search')
            ->will($this->returnValue(array_keys($this->messages)));

        $imapAdapter
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnCallback(function ($id) {
                return $this->createImapStorageMessage($id);
            }));


        $messages = $this->imapHelper->fetchMails($imapAdapter, [], true, true);

        $this->assertEquals($asserts, $messages);
    }

    /**
     * @param string $raw
     *
     * @return Message
     */
    protected function createImapRawMessage($raw)
    {
        return new Message(['raw' => $raw]);
    }
}
