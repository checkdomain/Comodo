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
    /**
     * @param ImapWithSearch $imap
     * @param string          $messages
     * @param string          $search
     * @param Folder          $folders
     *
     * @return mixed
     */
    public function fetchMails(ImapWithSearch $imap, $messages, $search, Folder $folders = null)
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
            }

            $messages = $this->fetchMails($imap, $messages, $search, $folder);
        }

        return $messages;
    }

    /**
     * @param Message $message
     *
     * @return string
     */
    public function getPlainText(Message $message)
    {
        $text = null;
        if ($message->isMultipart()) {
            foreach ($message as $part) {
                try {
                    if (strtok($part->contentType, ';') == 'text/plain') {
                        $text = $part;
                    }
                } catch (\Exception $e) {
                }
            }
        } else {
            $text = $message->getContent();
        }

        return quoted_printable_decode(strip_tags($text));
    }


}