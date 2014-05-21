<?php
namespace Checkdomain\Comodo;

use Zend\Mail\Storage\Imap;

/**
* Class ImapWithSearch
 *
 * Extends the Zend Imap Class
*
* @package Checkdomain\Administration\CustomerBundle\Controller
*/
class ImapWithSearch extends Imap
{
    /**
    * Gives access to the search function
    *
    * @param array $params
    *
    * @return array message ids
    */
    public function search(array $params)
    {
        return $this->protocol->search($params);
    }
}