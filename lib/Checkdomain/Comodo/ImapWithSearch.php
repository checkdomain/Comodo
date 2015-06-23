<?php
namespace Checkdomain\Comodo;

use Zend\Mail\Storage\Imap;

/**
* Class ImapWithSearch
 *
 * Extends the Zend Imap Class
*/
class ImapWithSearch extends Imap
{
    /**
    * Gives access to the search function
    *
    * @param $params
    *
    * @return array message ids
    */
    public function search($params)
    {
        // For the search function, it must have the ability to send non-arrays
         return $this->protocol->search($params);
    }
}
