<?php
namespace Checkdomain\Comodo;

use Laminas\Mail\Storage\Imap;

/**
 * @package Checkdomain\Comodo
 */
class ImapExtension extends Imap
{
    /**
     * @param array $params
     *
     * @return array
     */
    public function search(array $params)
    {
        // For the search function, it must have the ability to send non-arrays
        return $this->protocol->search($params);
    }
}
