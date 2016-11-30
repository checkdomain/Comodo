<?php
namespace Checkdomain\Comodo\Tests;

use Checkdomain\Comodo\CommunicationAdapter;
use Checkdomain\Comodo\ImapHelper;
use Checkdomain\Comodo\ImapAdapter;
use Checkdomain\Comodo\Model\Account;
use Checkdomain\Comodo\Util;
use Zend\Mail\Storage\Message;

class ImapHelperTest extends AbstractTest
{
    /**
     * @var ImapHelper
     */
    private $imapHelper;

    /**
     * @var array
     */
    private $messages = [
        [
            'subject'     => 'ORDER #12456789 - Your PositiveSSL Certificate for test.test.de',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Your PositiveSSL Certificate for test.test.de is attached!',
            'type'        => 'issued',
            'domainName'  => 'test.test.de',
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'ORDER #12456789 - Your PositiveSSL Certificate for *.öäüäö.de',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Your PositiveSSL Certificate for *.öäüäö.de is attached!',
            'type'        => 'issued',
            'domainName'  => '*.öäüäö.de',
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'CONFIRMATION',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Thank you for placing your order. Your details have been passed to Comodo, who will be validating your order. Your Order Number is 12456789.
                            PositiveSSL Certificate for
                            www.test.de. Please quote this Order Number in all correspondence. You have purchased:',
            'type'        => 'confirmation',
            'domainName'  => 'www.test.de',
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'Customer certificate expiry warning (1 days)',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Domain:- test.test.co.uk

                            The Issuance email address for this certificate was cdrobotcomodo@checkdomain.de and the order number was 12456789',
            'type'        => '1_expiry',
            'domainName'  => 'test.test.co.uk',
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'Customer certificate expiry warning (30 days)',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Domain:- test.test.co.uk

                            The Issuance email address for this certificate was cdrobotcomodo@checkdomain.de and the order number was 12456789',
            'type'        => '30_expiry',
            'domainName'  => 'test.test.co.uk',
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'Customer certificate expiry warning (60 days)',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Domain:- test.test.co.uk

                            The Issuance email address for this certificate was cdrobotcomodo@checkdomain.de and the order number was 12456789',
            'type'        => '60_expiry',
            'domainName'  => 'test.test.co.uk',
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'Information Required Order 12456789',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Thank you for your recent order.

                            We have begun validating your information so that we can issue your order. The following is the account information you submitted:

                            Company: privat
                            Domain Name: test.test.de ',
            'type'        => 'information_required',
            'domainName'  => 'test.test.de',
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'Trashmail',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Just some trash',
            'type'        => null,
            'domainName'  => null,
            'orderNumber' => null,
        ],
        [
            'subject'     => 'Trash for Order-Number: #12456789',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Just some trash',
            'type'        => null,
            'domainName'  => null,
            'orderNumber' => '12456789',
        ],
        [
            'subject'     => 'Trash',
            'date'        => '2014-05-04 12:43:00',
            'plainText'   => 'Random stuff see more at domain www.stuff.com',
            'type'        => null,
            'domainName'  => 'www.stuff.com',
            'orderNumber' => null,
        ],
    ];

    /**
     * @param string $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->imapHelper = new ImapHelper();
    }


    /**
     * @param $id
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function createImapStorageMessage($id)
    {
        $messageData = $this->messages[$id];

        $message = $this->getMockBuilder(Message::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $message
            ->expects($this->any())
            ->method('getHeader')
            ->will($this->returnCallback(function ($index, $type) use ($messageData) {
                if($type == 'string') {
                    if ($index == 'subject') {
                        return utf8_encode($messageData[$index]);
                    } else {
                        return $messageData[$index];
                    }
                } else {
                    return null;
                }
            }));

        $message
            ->expects($this->any())
            ->method('isMultipart')
            ->will($this->returnValue(false));

        $message
            ->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($this->messages[$id]['plainText']));

        $message
            ->expects($this->any())
            ->method('getHeaderField')
            ->will($this->returnValue('text/plain'))
            ->with($this->equalTo('Content-Type'));

        return $message;
    }

    /**
     * @param $raw
     *
     * @return Message
     */
    protected function createImapRawMessage($raw)
    {
        return new Message(array('raw' => $raw));
    }

    /**
     * TThis test is only for validating correct decoding of the message (subject/plaintext/received)
     */
    public function testMailParse()
    {
        $raw = "UmV0dXJuLVBhdGg6IDxzdXBwb3J0QGNvbW9kby5jb20+ClgtT3JpZ2luYWwtVG86IHNzbEB0ZXN0LmRlCkRlbGl2ZXJlZC1UbzogY2Rzc2xAaG9zdDJjMS50ZXN0LmRlClJlY2VpdmVkOiBmcm9tIG14cDAubmljZ2F0ZS5jb20gKG14cDAubmljZ2F0ZS5jb20gWzE4OC40MC42OS43Nl0pCiAgICBieSBob3MudGVzdC5kZSAoUG9zdGZpeCkgd2l0aCBFU01UUFMgaWQgMDAxNjU3RDMKICAgIGZvciA8c3NsQHRlc3QuZGU+OyBXZWQsICA5IEp1bCAyMDE0IDE2OjAwOjM0ICswMjAwIChDRVNUKQpSZWNlaXZlZDogZnJvbSBtY21haWwyLm1jci5jb2xvLmNvbW9kby5uZXQgKG1jbWFpbDIubWNyLmNvbG8uY29tb2RvLm5ldCBbSVB2NjoyYTAyOjE3ODg6NDAyOjFjODg6OmMwYTg6ODhjY10pCiAgICBieSBteHAwLm5pY2dhdGUuY29tIChQb3N0Zml4KSB3aXRoIEVTTVRQUyBpZCAxMDUxOUUyODAwQgogICAgZm9yIDxzc2xAdGVzdC5kZT47IFdlZCwgIDkgSnVsIDIwMTQgMTY6MDA6MzIgKzAyMDAgKENFU1QpClJlY2VpdmVkOiAocW1haWwgMTk3MjkgaW52b2tlZCBieSB1aWQgMTAwOCk7IDkgSnVsIDIwMTQgMTQ6MDA6MjkgLTAwMDAKUmVjZWl2ZWQ6IGZyb20gb3JhY2xlb25lX21jci5tY3IuY29sby5jb21vZG8ubmV0IChIRUxPIG1haWwuY29sby5jb21vZG8ubmV0KSAoMTkyLjE2OC4xMjguMjEpCiAgICBieSBtY21haWwyLm1jci5jb2xvLmNvbW9kby5uZXQgKHFwc210cGQvMC44NCkgd2l0aCBTTVRQOyBXZWQsIDA5IEp1bCAyMDE0IDE1OjAwOjI5ICswMTAwClN1YmplY3Q6IE9SREVSICMxMjM0NTY3OCAtIENPTkZJUk1BVElPTgpGcm9tOiAiQ29tb2RvIFNlY3VyaXR5IFNlcnZpY2VzIiA8bm9yZXBseV9zdXBwb3J0QGNvbW9kby5jb20+Ck1JTUUtVmVyc2lvbjogMS4wCkNvbnRlbnQtVHlwZTogbXVsdGlwYXJ0L2FsdGVybmF0aXZlOyBib3VuZGFyeT0iKEFsdGVybmF0aXZlQm91bmRhcnkpIgpDb250ZW50LVRyYW5zZmVyLUVuY29kaW5nOiA3Yml0ClRvOiAiVGVzdG1hc3RlciIgPHNzbEB0ZXN0LmRlPgpEYXRlOiBXZWQsIDA5IEp1bCAyMDE0IDEzOjU5OjUxICswMDAwCk1lc3NhZ2UtSUQ6IDw0TklyTVNzZmIyR0NQZll5VUk1bGFBQG1jbWFpbDIubWNyLmNvbG8uY29tb2RvLm5ldD4KWC1Qcm94eS1NYWlsU2Nhbm5lci1JbmZvcm1hdGlvbjogUGxlYXNlIGNvbnRhY3QgdGhlIElTUCBmb3IgbW9yZSBpbmZvcm1hdGlvbgpYLVByb3h5LU1haWxTY2FubmVyLUlEOiAxMDUxOUUyODAwQi5BQzg1RApYLVByb3h5LU1haWxTY2FubmVyOiBGb3VuZCB0byBiZSBjbGVhbgpYLVByb3h5LU1haWxTY2FubmVyLVNwYW1DaGVjazogbm90IHNwYW0sIFNwYW1Bc3Nhc3NpbiAobm90IGNhY2hlZCwKICAgIHNjb3JlPTEuNjI1LCByZXF1aXJlZCA1Ljc1LCBhdXRvbGVhcm49ZGlzYWJsZWQsIEhUTUxfTUVTU0FHRSAwLjAwLAogICAgU1BGX0hFTE9fUEFTUyAtMC4wMCwgU1VCSl9BTExfQ0FQUyAxLjYyKQpYLVByb3h5LU1haWxTY2FubmVyLVNwYW1TY29yZTogMQpYLVByb3h5LU1haWxTY2FubmVyLUZyb206IHN1cHBvcnRAY29tb2RvLmNvbQpYLVByb3h5LU1haWxTY2FubmVyLVRvOiBzc2xAdGVzdC5kZQpYLVNwYW0tU3RhdHVzOiBObwoKLS0oQWx0ZXJuYXRpdmVCb3VuZGFyeSkKQ29udGVudC1UeXBlOiB0ZXh0L3BsYWluOyBjaGFyc2V0PVVURi04CkNvbnRlbnQtVHJhbnNmZXItRW5jb2Rpbmc6IDhiaXQKCnRlc3QKCi0tKEFsdGVybmF0aXZlQm91bmRhcnkpCkNvbnRlbnQtVHlwZTogdGV4dC9odG1sOyBjaGFyc2V0PVVURi04CkNvbnRlbnQtVHJhbnNmZXItRW5jb2Rpbmc6IDhiaXQKCjxodG1sPgo8aGVhZD4KPC9oZWFkPgo8Ym9keT4KdGVzdAo8L2JvZHk+CjwvaHRtbD4KCi0tKEFsdGVybmF0aXZlQm91bmRhcnkpLS0KCg==";

        $imapAdapter   = $this->createImapAdpater();

        /** @var \PHPUnit_Framework_MockObject_MockObject $imapExtension */
        $imapExtension = $imapAdapter->getInstance();

        $imapExtension
            ->expects($this->any())
            ->method('search')
            ->will($this->returnValue(array(0)));

        $imapExtension
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($this->createImapRawMessage(base64_decode($raw))));

        $messages = $this->imapHelper->fetchMails($imapAdapter, array(), null, null, true, true);

        $message = $messages[0];

        $this->assertEquals('ORDER #12345678 - CONFIRMATION', $message['subject']);
        $this->assertEquals(1404914391, $message['received']);
        $this->assertEquals("test\n\n", $message['plainText']);
    }

    /**
     * This test is only for validating correct decoding of the attachments
     */
    public function testMailAttachmentParse()
    {
        $raw = "UmV0dXJuLVBhdGg6IDxzdXBwb3J0QGNvbW9kby5jb20+ClgtT3JpZ2luYWwtVG86IHNzbEB0ZXN0LmRlCkRlbGl2ZXJlZC1UbzogY2Rzc2xAaG9zdDJjMS50ZXN0LmRlClJlY2VpdmVkOiBmcm9tIG14cDAubmljZ2F0ZS5jb20gKG14cDAubmljZ2F0ZS5jb20gWzE4OC40MC42OS43Nl0pCiAgICBieSBob3N0MmMxLnRlc3QuZGUgKFBvc3RmaXgpIHdpdGggRVNNVFBTIGlkIEREMjA5MkIzCiAgICBmb3IgPHNzbEB0ZXN0LmRlPjsgTW9uLCAgNyBKdWwgMjAxNCAxMjozMjozOSArMDIwMCAoQ0VTVCkKUmVjZWl2ZWQ6IGZyb20gbWNtYWlsMi5tY3IuY29sby5jb21vZG8ubmV0IChtY21haWwyLm1jci5jb2xvLmNvbW9kby5uZXQgW0lQdjY6MmEwMjoxNzg4OjQwMjoxYzg4OjpjMGE4Ojg4Y2NdKQogICAgYnkgbXhwMC5uaWNnYXRlLmNvbSAoUG9zdGZpeCkgd2l0aCBFU01UUFMgaWQgNEY5QjNFMjgwMEQKICAgIGZvciA8c3NsQHRlc3QuZGU+OyBNb24sICA3IEp1bCAyMDE0IDEyOjMyOjMwICswMjAwIChDRVNUKQpSZWNlaXZlZDogKHFtYWlsIDExMTYyIGludm9rZWQgYnkgdWlkIDEwMDgpOyA3IEp1bCAyMDE0IDEwOjMyOjMwIC0wMDAwClJlY2VpdmVkOiBmcm9tIG9yYWNsZW9uZV9tY3IubWNyLmNvbG8uY29tb2RvLm5ldCAoSEVMTyBtYWlsLmNvbG8uY29tb2RvLm5ldCkgKDE5Mi4xNjguMTI4LjIxKQogICAgYnkgbWNtYWlsMi5tY3IuY29sby5jb21vZG8ubmV0IChxcHNtdHBkLzAuODQpIHdpdGggU01UUDsgTW9uLCAwNyBKdWwgMjAxNCAxMTozMjozMCArMDEwMApTdWJqZWN0OiBPUkRFUiAjMTQ3NjU2MDIgLSBZb3VyIFBvc2l0aXZlU1NMIENlcnRpZmljYXRlIGZvciBmaW5hbHRlc3QudG9iaWFzLW5pdHNjaGUuZGUKRnJvbTogIkNvbW9kbyBTZWN1cml0eSBTZXJ2aWNlcyIgPG5vcmVwbHlfc3VwcG9ydEBjb21vZG8uY29tPgpUbzogInNzbEB0ZXN0LmRlIiA8c3NsQHRlc3QuZGU+CkRhdGU6IE1vbiwgMDcgSnVsIDIwMTQgMTA6MzI6MjAgKzAwMDAKTUlNRS1WZXJzaW9uOiAxLjAKQ29udGVudC1UcmFuc2Zlci1FbmNvZGluZzogN2JpdApDb250ZW50LVR5cGU6IG11bHRpcGFydC9taXhlZDsgYm91bmRhcnk9IihBbHRlcm5hdGl2ZUJvdW5kYXJ5MikiCk1lc3NhZ2UtSUQ6IDxJbkdKYlNKK0h2QUFpTUhjN1MxVTJRQG1jbWFpbDIubWNyLmNvbG8uY29tb2RvLm5ldD4KWC1Qcm94eS1NYWlsU2Nhbm5lci1JbmZvcm1hdGlvbjogUGxlYXNlIGNvbnRhY3QgdGhlIElTUCBmb3IgbW9yZSBpbmZvcm1hdGlvbgpYLVByb3h5LU1haWxTY2FubmVyLUlEOiA0RjlCM0UyODAwRC5BMDM1MgpYLVByb3h5LU1haWxTY2FubmVyOiBGb3VuZCB0byBiZSBjbGVhbgpYLVByb3h5LU1haWxTY2FubmVyLVNwYW1DaGVjazogbm90IHNwYW0sIFNwYW1Bc3Nhc3NpbiAobm90IGNhY2hlZCwgc2NvcmU9MSwKICAgIHJlcXVpcmVkIDUuNzUsIGF1dG9sZWFybj1kaXNhYmxlZCwgREVBUl9FTUFJTCAxLjAwLAogICAgSFRNTF9NRVNTQUdFIDAuMDAsIFNQRl9IRUxPX1BBU1MgLTAuMDApClgtUHJveHktTWFpbFNjYW5uZXItU3BhbVNjb3JlOiAxClgtUHJveHktTWFpbFNjYW5uZXItRnJvbTogc3VwcG9ydEBjb21vZG8uY29tClgtUHJveHktTWFpbFNjYW5uZXItVG86IHNzbEB0ZXN0LmRlClgtU3BhbS1TdGF0dXM6IE5vCgotLShBbHRlcm5hdGl2ZUJvdW5kYXJ5MikKQ29udGVudC1UeXBlOiBtdWx0aXBhcnQvYWx0ZXJuYXRpdmU7IGJvdW5kYXJ5PSIoQWx0ZXJuYXRpdmVCb3VuZGFyeSkiCgotLShBbHRlcm5hdGl2ZUJvdW5kYXJ5KQpDb250ZW50LVR5cGU6IHRleHQvcGxhaW47IGNoYXJzZXQ9VVRGLTgKQ29udGVudC1UcmFuc2Zlci1FbmNvZGluZzogOGJpdAoKdGVzdAoKLS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSUZTekNDQkRPZ0F3SUJBZ0lRS0lXTnpIYTFVNUFDUkJIY0dBNGdLVEFOQmdrcWhraUc5dzBCQVFVRkFEQnoKTVFzd0NRWURWUVFHRXdKSFFqRWJNQmtHQTFVRUNCTVNSM0psWVhSbGNpQk5ZVzVqYUdWemRHVnlNUkF3RGdZRApWUVFIRXdkVFlXeG1iM0prTVJvd0dBWURWUVFLRXhGRFQwMVBSRThnUTBFZ1RHbHRhWFJsWkRFWk1CY0dBMVVFCkF4TVFVRzl6YVhScGRtVlRVMHdnUTBFZ01qQWVGdzB4TkRBM01EY3dNREF3TURCYUZ3MHhOVEEzTURjeU16VTUKTlRsYU1JR0VNU0V3SHdZRFZRUUxFeGhFYjIxaGFXNGdRMjl1ZEhKdmJDQldZV3hwWkdGMFpXUXhJekFoQmdOVgpCQXNUR2todmMzUmxaQ0JpZVNCRGFHVmphMlJ2YldGcGJpQkhiV0pJTVJRd0VnWURWUVFMRXd0UWIzTnBkR2wyClpWTlRUREVrTUNJR0ExVUVBeE1iWm1sdVlXeDBaWE4wTG5SdlltbGhjeTF1YVhSelkyaGxMbVJsTUlJQklqQU4KQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBMzNWTVpnd2ZOc0hoOHZlZTBkdWNoVUhsQ3drWApTcGd0VW9iVFFCZ2dEbk1HNlVUbEltcEFVVWNORzlYbWZHVlVFdE15UXd4bCthc0VkaFpoYnF1VVdqcmdNV2FlCitWZlFtN3kwQlVNNWxFRlpOdTVkWjVJblRVa2hQOHZDQnJGUVVoSGQwU1FuNjdTUWFscVJVZFNOVjBCRDBjWUYKbTJHZmtyUlZyWWNjai9JOFIxOVV6eDlNNXZwNGY4Tmw0YytuVTJzVTVLSkFZQ2JJdFBDY0lDVENaQzdQNmJ6TwprenArb1ZpbmVmeVVZcGRXMFVhd21vWmVsV2R4cllNaUZjYW5oZTZFQnI0WUg4Ny9CWWVReUcxZHJhMkhiMFdTCmpoNGxOMUFpZXRwK0E2S0I1ZHJPM1JQaGRDcHhFd2N6TGMyczFVZTl4dmdNdnd6NzM3OHpYSWNxbHdJREFRQUIKbzRJQnh6Q0NBY013SHdZRFZSMGpCQmd3Rm9BVW1lUkFYMnNVWGo0RjJkM1RZMVQ4WXJqM0FLd3dIUVlEVlIwTwpCQllFRkd1SzQ3blFNTFdNdmFOVDlvRjNzeFJObCtzck1BNEdBMVVkRHdFQi93UUVBd0lGb0RBTUJnTlZIUk1CCkFmOEVBakFBTUIwR0ExVWRKUVFXTUJRR0NDc0dBUVVGQndNQkJnZ3JCZ0VGQlFjREFqQlFCZ05WSFNBRVNUQkgKTURzR0N5c0dBUVFCc2pFQkFnSUhNQ3d3S2dZSUt3WUJCUVVIQWdFV0htaDBkSEE2THk5M2QzY3VjRzl6YVhScApkbVZ6YzJ3dVkyOXRMME5RVXpBSUJnWm5nUXdCQWdFd093WURWUjBmQkRRd01qQXdvQzZnTElZcWFIUjBjRG92CkwyTnliQzVqYjIxdlpHOWpZUzVqYjIwdlVHOXphWFJwZG1WVFUweERRVEl1WTNKc01Hd0dDQ3NHQVFVRkJ3RUIKQkdBd1hqQTJCZ2dyQmdFRkJRY3dBb1lxYUhSMGNEb3ZMMk55ZEM1amIyMXZaRzlqWVM1amIyMHZVRzl6YVhScApkbVZUVTB4RFFUSXVZM0owTUNRR0NDc0dBUVVGQnpBQmhoaG9kSFJ3T2k4dmIyTnpjQzVqYjIxdlpHOWpZUzVqCmIyMHdSd1lEVlIwUkJFQXdQb0liWm1sdVlXeDBaWE4wTG5SdlltbGhjeTF1YVhSelkyaGxMbVJsZ2g5M2QzY3UKWm1sdVlXeDBaWE4wTG5SdlltbGhjeTF1YVhSelkyaGxMbVJsTUEwR0NTcUdTSWIzRFFFQkJRVUFBNElCQVFDbwpPL1B5cDJHTHdmWDBlbmxnVnJyVVZUYmh2NVBQV1pBajQ2Z3pidVhVYUY5a2hMNy9DQ1VJZEc3ekdvdGlDWlVZCklDOUJrYkc2S0MrWEpWdGgzam50a2JNN0ZaQzFCUHk5Q3VtSmNpWlVHdUJzem12c1k4N1VVL2FVZ0t2SlpOaVoKZGwxMW92dnBUQTJEdW5ISmtyOXF0eStFQkMyTHY4aFNwTHZlMS9KVFlwYXBqZTRSeXlrcjFYY0phaGRnOEtjOQpXN1U0SmY0aWNoV2lQTWFoSnBnZ0NrVGE0eGYybnFXdTMvVG1jeThFbjNnVWZZNEZzRTg5eWJmVDZzT0J0SVdUCnNma3BpUm5vL1FWUTBQZW4yTCtzVGFCZVZ5YmswN0IrRGdMRWxGUmxuS2NDWFBzUU9vVVdlcHJ3VkNVa1E2VE4KNFNscmRxOHY5T2lkUzg2Z01CRU4KLS0tLS1FTkQgQ0VSVElGSUNBVEUtLS0tLQoKLS0oQWx0ZXJuYXRpdmVCb3VuZGFyeSkKQ29udGVudC1UeXBlOiB0ZXh0L2h0bWw7IGNoYXJzZXQ9SVNPLTg4NTktMQpDb250ZW50LVRyYW5zZmVyLUVuY29kaW5nOiA3Yml0Cgo8aHRtbD4KPGhlYWQ+Cgo8L2hlYWQ+Cjxib2R5Pgp0ZXN0CjwvYm9keT4KPC9odG1sPgoKLS0oQWx0ZXJuYXRpdmVCb3VuZGFyeSktLQoKLS0oQWx0ZXJuYXRpdmVCb3VuZGFyeTIpCkNvbnRlbnQtVHlwZTogYXBwbGljYXRpb24veC16aXAtY29tcHJlc3NlZDsgbmFtZT0iZmluYWx0ZXN0X3RvYmlhcy1uaXRzY2hlX2RlLnppcCIKQ29udGVudC1UcmFuc2Zlci1FbmNvZGluZzogYmFzZTY0CkNvbnRlbnQtRGlzcG9zaXRpb246IGF0dGFjaG1lbnQ7IGZpbGVuYW1lPSJmaW5hbHRlc3RfdG9iaWFzLW5pdHNjaGVfZGUuemlwIgoKVUVzREJBb0FBQUFBQUFBQVVFQ1JJcVYrM1FZQUFOMEdBQUFsQUFBQVptbHVZV3gwWlhOMFgzUnZZbWxoY3kxdQphWFJ6WTJobFgyUmxMbU5oTFdKMWJtUnNaUzB0TFMwdFFrVkhTVTRnUTBWU1ZFbEdTVU5CVkVVdExTMHRMUXBOClNVbEZOVlJEUTBFNE1tZEJkMGxDUVdkSlVVSXlPRk5TYjBaR2JrTnFWbE5PWVZoNFFUUkJSM3BCVGtKbmEzRm8KYTJsSE9YY3dRa0ZSVlVaQlJFSjJDazFSYzNkRFVWbEVWbEZSUjBWM1NsUlNWRVZWVFVKSlIwRXhWVVZEYUUxTQpVVmRTYTFaSVNqRmpNMUZuVVZWSmVFcHFRV3RDWjA1V1FrRnpWRWhWUm1zS1drWlNlV1JZVGpCSlJWWTBaRWRXCmVXSnRSbk5KUmxKVlZVTkNUMXBZVWpOaU0wcHlUVk5KZDBsQldVUldVVkZFUlhoc1FscEhVbFZqYmxaNlpFTkMKUmdwbFNGSnNZMjAxYUdKRFFrUlJVMEpUWWpJNU1FMUNORmhFVkVWNVRVUkplRTVxUVhkTlJFRjNUVVp2V0VSVQpTWGROUkZWNlRVUkZkMDVFWjNwUFJtOTNDbU42UlV4TlFXdEhRVEZWUlVKb1RVTlNNRWw0UjNwQldrSm5UbFpDClFXZFVSV3RrZVZwWFJqQmFXRWxuVkZkR2RWa3lhR3hqTTFKc1kycEZVVTFCTkVjS1FURlZSVUo0VFVoVk1rWnoKV20wNWVWcEVSV0ZOUW1kSFFURlZSVU5vVFZKUk1EbE9WREJTVUVsRlRrSkpSWGh3WWxkc01GcFhVWGhIVkVGWQpRbWRPVmdwQ1FVMVVSVVpDZG1NeWJEQmhXRnBzVlRGT1RVbEZUa0pKUkVsM1oyZEZhVTFCTUVkRFUzRkhVMGxpCk0wUlJSVUpCVVZWQlFUUkpRa1IzUVhkblowVkxDa0Z2U1VKQlVVUnZObXB1YWtseFlYRjFZMUZCTUU5bGNWcDYKZEVSQ056RlFhM1YxT0hablIycFJTek5uTnpCUmIzUmtRVFoyYjBKVlJqUldObUUwVW5NS1RtcGliRzk1VkdrdgphV2RDYTB4NldETlJLelZMTURWSlpIZFdjSEk1TlZoTlRFaHZLM2h2UkRscWVHSlZlRFpvUVZWc2IyTnVVRmROCmVYUkVjVlJqZVFwVlp5dDFTakZaZUUxSFEzUjVZakY2VEVSdWRXdE9hREZ6UTFWb1dVaHpjV1ozVERsbmIxVm0KWkVVclUwNUlUbU5JVVVObmMwMUVjVzFQU3l0QlVsSlpDa1o1WjJscGJtUmtWVU5ZVG0xdGVXMDFVWHBzY1hscQpSSE5wUTBvNFFXTnJTSEJZUTB4elJHdzJaWG95VUZKSlNGTkVNMU4zZVU1WFVXVjZWRE42Vmt3S2VVOW1NbWhuClZsTkZSVTloYWtKa09HazJjVGhsVDBSM1VsUjFjMmRHV0N0TFNsQm9RMmhHYnpsR1NsaGlMelZKUXpGMFpFZHQKY0c1ak5XMURkRW8xUkFwWlJEZElWM2x2VTJKb2NuVjVlbTExZDNwWFpIRk1lR1J6UXk5RVFXZE5Ra0ZCUjJwbgpaMFl6VFVsSlFtTjZRV1pDWjA1V1NGTk5SVWRFUVZkblFsTjBDblphYURaT1RGRnRPUzl5UlVwc1ZIWkJOek5uClNrMTBWVWRxUVdSQ1owNVdTRkUwUlVablVWVnRaVkpCV0RKelZWaHFORVl5WkROVVdURlVPRmx5YWpNS1FVdDMKZDBSbldVUldVakJRUVZGSUwwSkJVVVJCWjBWSFRVSkpSMEV4VldSRmQwVkNMM2RSU1UxQldVSkJaamhEUVZGQgpkMFZSV1VSV1VqQm5Ra0Z2ZHdwRFJFRkhRbWRTVmtoVFFVRk5SVkZIUVRGVlpFaDNVVGxOUkhOM1QyRkJNMjlFClYwZE5NbWd3WkVoQk5reDVPV3BqYlhkMVpGaE9iR051VW5sa1dFNHdDa3h0VG5aaVV6bENXa2RTVldOdVZucGsKUlZZMFpFZFdlV0p0Um5OUk1FWlRZakk1TUV4dFRubGlSRU5DYzNkWlNVdDNXVUpDVVZWSVFWRkZSV2RoV1hjSwpaMkZOZDFCM1dVbExkMWxDUWxGVlNFMUJTMGROTW1nd1pFaEJOa3g1T1dwamJsRjFaRmhPYkdOdVVubGtXRTR3ClRHMU9kbUpUT1VKYVIxSlZZMjVXZWdwa1JWWTBaRWRXZVdKdFJuTlJNRVpUWWpJNU1FeHVRVE5aZWtFMVFtZG4KY2tKblJVWkNVV04zUVc5WmRHRklVakJqUkc5MlRESk9lV1JETlRGak1sWjVDbVJJU2pGak0xRjFXVEk1ZEV3dwpSbXRhUmxKNVpGaE9NRlpXVWs5Vk1HUkVVVEJGZFZrelNqQk5RMVZIUTBOelIwRlJWVVpDZWtGQ2FHaHNiMlJJClVuY0tUMms0ZG1JeVRucGpRelV4WXpKV2VXUklTakZqTTFGMVdUSTVkRTFCTUVkRFUzRkhVMGxpTTBSUlJVSkMKVVZWQlFUUkpRa0ZSUTJOT2RVNVBjblpIU3dwMU1ubFlha2s1VEZvNVEyWXlTVk54Ym5sR1prNWhSbUo0UTNScQpSR1ZwT0dReE1tNTRSR1k1VTNreVpUWkNNWEJ2WTBORmVrNUdkR2t2VDBKNU5UbE1DbVJNUWtwTGFraHZUakJFCmNrZzViVmh2ZUc5U01WTmhibUpuS3pZeFlqUnpMMkpUVWxwT2VTdFBlR3hSUkZoeFZqaDNVVlJ4WW5SSVJEUjAKWXpCaGVrTUtaVE5qYUZWT01XSnhLemN3Y0hScVZWTnNUbkpVWVRJMGVVOW1iVlZzYUU1Uk1IcERiMmxPVUVSegpRV2RQWVM5bVZEQktZa2gwVFVvNVFtZEtWMU55V2dvMlJXOVpkbnBNTnl0cE1XdHBOR1pMVjNsMmIzVkJkQ3QyCmFHTlRlSGRQUTB0aE9WbHlORmRGV0ZRd1N6TjVUbEozT0RKMlJVd3JRV0ZZWlZKRGF5OXNDblYxUjNSdE9EZG0KVFRBMGQwOHJiVkJhYml0REsyMTJOakkyVUVGamQwUnFNV2hMZGxSbVNWQlhhRkpTU0RJeU5HaHZSbWxDT0RWagpZM05LVURneFkzRUtZMlJ1Vld3MFdHMUhSazh6Q2kwdExTMHRSVTVFSUVORlVsUkpSa2xEUVZSRkxTMHRMUzBLClVFc0RCQW9BQUFBQUFBQUE1MFRDdzhFOVp3Y0FBR2NIQUFBZkFBQUFabWx1WVd4MFpYTjBYM1J2WW1saGN5MXUKYVhSelkyaGxYMlJsTG1OeWRDMHRMUzB0UWtWSFNVNGdRMFZTVkVsR1NVTkJWRVV0TFMwdExRcE5TVWxHVTNwRApRMEpFVDJkQmQwbENRV2RKVVV0SlYwNTZTR0V4VlRWQlExSkNTR05IUVRSblMxUkJUa0puYTNGb2EybEhPWGN3ClFrRlJWVVpCUkVKNkNrMVJjM2REVVZsRVZsRlJSMFYzU2toUmFrVmlUVUpyUjBFeFZVVkRRazFUVWpOS2JGbFkKVW14amFVSk9XVmMxYW1GSFZucGtSMVo1VFZKQmQwUm5XVVFLVmxGUlNFVjNaRlJaVjNodFlqTkthMDFTYjNkSApRVmxFVmxGUlMwVjRSa1JVTURGUVVrVTRaMUV3UldkVVIyeDBZVmhTYkZwRVJWcE5RbU5IUVRGVlJRcEJlRTFSClZVYzVlbUZZVW5Ca2JWWlVWVEIzWjFFd1JXZE5ha0ZsUm5jd2VFNUVRVE5OUkdOM1RVUkJkMDFFUW1GR2R6QjQKVGxSQk0wMUVZM2xOZWxVMUNrNVViR0ZOU1VkRlRWTkZkMGgzV1VSV1VWRk1SWGhvUldJeU1XaGhWelJuVVRJNQpkV1JJU25aaVEwSlhXVmQ0Y0ZwSFJqQmFWMUY0U1hwQmFFSm5UbFlLUWtGelZFZHJhSFpqTTFKc1drTkNhV1ZUClFrUmhSMVpxWVRKU2RtSlhSbkJpYVVKSVlsZEtTVTFTVVhkRloxbEVWbEZSVEVWM2RGRmlNMDV3WkVkc01ncGEKVms1VVZFUkZhMDFEU1VkQk1WVkZRWGhOWWxwdGJIVlpWM2d3V2xoT01FeHVVblpaYld4b1kza3hkV0ZZVW5wWgpNbWhzVEcxU2JFMUpTVUpKYWtGT0NrSm5hM0ZvYTJsSE9YY3dRa0ZSUlVaQlFVOURRVkU0UVUxSlNVSkRaMHREClFWRkZRVE16VmsxYVozZG1Ubk5JYURoMlpXVXdaSFZqYUZWSWJFTjNhMWdLVTNCbmRGVnZZbFJSUW1kblJHNU4KUnpaVlZHeEpiWEJCVlZWalRrYzVXRzFtUjFaVlJYUk5lVkYzZUd3cllYTkZaR2hhYUdKeGRWVlhhbkpuVFZkaApaUW9yVm1aUmJUZDVNRUpWVFRWc1JVWmFUblUxWkZvMVNXNVVWV3RvVURoMlEwSnlSbEZWYUVoa01GTlJialkzClUxRmhiSEZTVldSVFRsWXdRa1F3WTFsR0NtMHlSMlpyY2xKV2NsbGpZMm92U1RoU01UbFZlbmc1VFRWMmNEUm0KT0U1c05HTXJibFV5YzFVMVMwcEJXVU5pU1hSUVEyTkpRMVJEV2tNM1VEWmllazhLYTNwd0syOVdhVzVsWm5sVgpXWEJrVnpCVllYZHRiMXBsYkZka2VISlpUV2xHWTJGdWFHVTJSVUp5TkZsSU9EY3ZRbGxsVVhsSE1XUnlZVEpJCllqQlhVd3BxYURSc1RqRkJhV1YwY0N0Qk5rdENOV1J5VHpOU1VHaGtRM0I0UlhkamVreGpNbk14VldVNWVIWm4KVFhaM2VqY3pOemg2V0VsamNXeDNTVVJCVVVGQ0NtODBTVUo0ZWtORFFXTk5kMGgzV1VSV1VqQnFRa0puZDBadgpRVlZ0WlZKQldESnpWVmhxTkVZeVpETlVXVEZVT0ZseWFqTkJTM2QzU0ZGWlJGWlNNRThLUWtKWlJVWkhkVXMwCk4yNVJUVXhYVFhaaFRsUTViMFl6YzNoU1Rtd3JjM0pOUVRSSFFURlZaRVIzUlVJdmQxRkZRWGRKUm05RVFVMUMKWjA1V1NGSk5RZ3BCWmpoRlFXcEJRVTFDTUVkQk1WVmtTbEZSVjAxQ1VVZERRM05IUVZGVlJrSjNUVUpDWjJkeQpRbWRGUmtKUlkwUkJha0pSUW1kT1ZraFRRVVZUVkVKSUNrMUVjMGREZVhOSFFWRlJRbk5xUlVKQlowbElUVU4zCmQwdG5XVWxMZDFsQ1FsRlZTRUZuUlZkSWJXZ3daRWhCTmt4NU9UTmtNMk4xWTBjNWVtRllVbkFLWkcxV2VtTXkKZDNWWk1qbDBUREJPVVZWNlFVbENaMXB1WjFGM1FrRm5SWGRQZDFsRVZsSXdaa0pFVVhkTmFrRjNiME0yWjB4SgpXWEZoU0ZJd1kwUnZkZ3BNTWs1NVlrTTFhbUl5TVhaYVJ6bHFXVk0xYW1JeU1IWlZSemw2WVZoU2NHUnRWbFJWCk1IaEVVVlJKZFZrelNuTk5SM2RIUTBOelIwRlJWVVpDZDBWQ0NrSkhRWGRZYWtFeVFtZG5ja0puUlVaQ1VXTjMKUVc5WmNXRklVakJqUkc5MlRESk9lV1JETldwaU1qRjJXa2M1YWxsVE5XcGlNakIyVlVjNWVtRllVbkFLWkcxVwpWRlV3ZUVSUlZFbDFXVE5LTUUxRFVVZERRM05IUVZGVlJrSjZRVUpvYUdodlpFaFNkMDlwT0haaU1rNTZZME0xCmFtSXlNWFphUnpscVdWTTFhZ3BpTWpCM1VuZFpSRlpTTUZKQ1JVRjNVRzlKWWxwdGJIVlpWM2d3V2xoT01FeHUKVW5aWmJXeG9ZM2t4ZFdGWVVucFpNbWhzVEcxU2JHZG9PVE5rTTJOMUNscHRiSFZaVjNnd1dsaE9NRXh1VW5aWgpiV3hvWTNreGRXRllVbnBaTW1oc1RHMVNiRTFCTUVkRFUzRkhVMGxpTTBSUlJVSkNVVlZCUVRSSlFrRlJRMjhLClR5OVFlWEF5UjB4M1psZ3daVzVzWjFaeWNsVldWR0pvZGpWUVVGZGFRV28wTm1kNlluVllWV0ZHT1d0b1REY3YKUTBOVlNXUkhOM3BIYjNScFExcFZXUXBKUXpsQ2EySkhOa3RESzFoS1ZuUm9NMnB1ZEd0aVRUZEdXa014UWxCNQpPVU4xYlVwamFWcFZSM1ZDYzNwdGRuTlpPRGRWVlM5aFZXZExka3BhVG1sYUNtUnNNVEZ2ZG5ad1ZFRXlSSFZ1ClNFcHJjamx4ZEhrclJVSkRNa3gyT0doVGNFeDJaVEV2U2xSWmNHRndhbVUwVW5sNWEzSXhXR05LWVdoa1p6aEwKWXprS1Z6ZFZORXBtTkdsamFGZHBVRTFoYUVwd1oyZERhMVJoTkhobU1tNXhWM1V6TDFSdFkzazRSVzR6WjFWbQpXVFJHYzBVNE9YbGlabFEyYzA5Q2RFbFhWQXB6Wm10d2FWSnVieTlSVmxFd1VHVnVNa3dyYzFSaFFtVldlV0pyCk1EZENLMFJuVEVWc1JsSnNia3RqUTFoUWMxRlBiMVZYWlhCeWQxWkRWV3RSTmxST0NqUlRiSEprY1RoMk9VOXAKWkZNNE5tZE5Ra1ZPQ2kwdExTMHRSVTVFSUVORlVsUkpSa2xEUVZSRkxTMHRMUzBLVUVzQkFoUUFDZ0FBQUFBQQpBQUJRUUpFaXBYN2RCZ0FBM1FZQUFDVUFBQUFBQUFBQUFRQWdBTGFCQUFBQUFHWnBibUZzZEdWemRGOTBiMkpwCllYTXRibWwwYzJOb1pWOWtaUzVqWVMxaWRXNWtiR1ZRU3dFQ0ZBQUtBQUFBQUFBQUFPZEV3c1BCUFdjSEFBQm4KQndBQUh3QUFBQUFBQUFBQkFDQUF0b0VnQndBQVptbHVZV3gwWlhOMFgzUnZZbWxoY3kxdWFYUnpZMmhsWDJSbApMbU55ZEZCTEJRWUFBQUFBQWdBQ0FLQUFBQURFRGdBQUFBQT0KCi0tKEFsdGVybmF0aXZlQm91bmRhcnkyKS0t";

        $imapAdapter   = $this->createImapAdpater();

        /** @var \PHPUnit_Framework_MockObject_MockObject $imapExtension */
        $imapExtension = $imapAdapter->getInstance();

        $imapExtension
            ->expects($this->any())
            ->method('search')
            ->will($this->returnValue(array(0)));

        $imapExtension
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue($this->createImapRawMessage(base64_decode($raw))));

        $messages = $this->imapHelper->fetchMails($imapAdapter, array(), null, null, true, true);

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
        $asserts = array();

        foreach ($this->messages as $id => $message) {
            $asserts[$id] = array();

            $asserts[$id]['id'] = $id;

            $asserts[$id]['folder']      = $this->createZendImapStorageFolder();
            $asserts[$id]['subject']     = $message['subject'];
            $asserts[$id]['received']    = strtotime($message['date']);
            $asserts[$id]['plainText']   = $message['plainText'];
            $asserts[$id]['attachments'] = null;
            $asserts[$id]['type']        = $message['type'];
            $asserts[$id]['domainName']  = $message['domainName'];
            $asserts[$id]['orderNumber'] = $message['orderNumber'];
        }

        $imapAdapter = $this->createImapAdpater();

        $imapAdapter
            ->expects($this->any())
            ->method('search')
            ->will($this->returnValue(array_keys($this->messages)));

        $testClass = $this;

        $imapAdapter
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnCallback(function ($id) use ($testClass) {
                return $testClass->createImapStorageMessage($id);
            }));


        $messages = $this->imapHelper->fetchMails($imapAdapter, array(), null, null, true, true);

        $this->assertEquals($asserts, $messages);
    }
}
