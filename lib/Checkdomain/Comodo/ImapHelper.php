<?php
namespace Checkdomain\Comodo;

use Zend\Mail\Storage\Message;
use Zend\Mail\Storage\Folder;

/**
 * Class ImapHelper
 *
 * @package Checkdomain\Administration\CustomerBundle\Controller
 */
class ImapHelper
{
    public static $subjects = array(
            'order_received' => 'Your order has been received',
            'information_required' => 'Information Required: ',
            'confirmation' => 'CONFIRMATION',
            '1_expiry' => 'Customer certificate expiry warning (1 days)',
            '7_expiry' => 'Customer certificate expiry warning (7 days)',
            '14_expiry' => 'Customer certificate expiry warning (14 days)',
            '30_expiry' => 'Customer certificate expiry warning (30 days)',
            '60_expiry' => 'Customer certificate expiry warning (60 days)'
    );

    public static $bodies = array(
        'success' => '/Your [a-zA-Z ]* Certificate for [a-zA-Z0-9üצה\.]* is attached!/'
    );

    /**
     * @param ImapWithSearch $imap
     * @param string          $messages
     * @param string          $search
     * @param Folder          $folders
     *
     * @return mixed
     */
    public function fetchMails(ImapWithSearch $imap, $messages, $search, Folder $folders = null, $assume = false)
    {
        if($folders === null) {
            $folders = $imap->getFolders();
        }

        foreach ($folders as $folder) {
            $imap->selectFolder($folder);
            $result = $imap->search($search);

            foreach ($result as $id) {
                $i = count($messages);

                $message = $imap->getMessage($id);

                $messages[$i]['id'] = $i;
                $messages[$i]['folder'] =$folder;
                $messages[$i]['subject'] = utf8_decode($message->subject);
                $messages[$i]['received'] = strtotime($message->date);
                $messages[$i]['plainText'] = $this->getPlainText($message);
                $messages[$i]['type'] = $this->getTypeOfMail($messages[$i]);

                if($assume)
                {
                    $messages[$i]['orderNumber'] = $this->assumeOrderNumber($messages[$i]);
                    $messages[$i]['domainName'] = $this->assumeDomainName($messages[$i]);
                }
            }

            $messages = $this->fetchMails($imap, $messages, $search, $folder);
        }

        return $messages;
    }

    /**
     * Tries to find out, what Comodo wanna tell us...
     *
     * @param array $mail
     * @return null|string
     */
    protected function getTypeOfMail($mail) {
        foreach(self::$subjects as $key => $subject) {
            if(stristr($mail['subject'], $subject) !== false) {
                return $key;
            }
        }

        foreach(self::$bodies as $key => $body) {
            if(preg_match($body, $mail['plainText'])) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param Message $message
     *
     * @return string
     */
    public function getPlainText(Message $message)
    {
        $text = null;
        try {
            if ($message->isMultipart()) {
                foreach ($message as $part) {
                    if (strtok($part->contentType, ';') == 'text/plain') {
                        $text = $part;
                    }
                }
            } else {
                $text = $message->getContent();
            }
        } catch (\Exception $e) {
        }

        return quoted_printable_decode(strip_tags($text));
    }

    /**
     * @param $message
     *
     * @return integer|null
     */
    protected function assumeOrderNumber($message)
    {
        $pattern = '/[order|\#][^0-9]+([0-9]{7,8})/i';
        $matches = array();
        preg_match($pattern, $message['subject'], $matches);

        if(!empty($matches[1])) {
            return $matches[1];
        }

        $matches = array();
        preg_match($pattern, $message['plainText'], $matches);

        if(!empty($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param $message
     *
     * @return string|null
     */
    protected function assumeDomainName($message)
    {
        $pattern = '/(certificate|for|domain|domain name)+[\s\W]*(([a-z0-9\_\-הüצß\*]+\.){1,3}[a-z]{2,63})/i';
        $matches = array();
        preg_match($pattern, $message['subject'], $matches);

        if(!empty($matches[3])) {
            return $matches[3];
        }

        $matches = array();
        preg_match($pattern, $message['plainText'], $matches);

        if(!empty($matches[3])) {
            return $matches[3];
        }

        return null;
    }
}