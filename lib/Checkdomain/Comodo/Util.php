<?php
namespace Checkdomain\Comodo;

use Checkdomain\Comodo\Model\Exception\AccountException;
use Checkdomain\Comodo\Model\Exception\ArgumentException;
use Checkdomain\Comodo\Model\Exception\CSRException;
use Checkdomain\Comodo\Model\Exception\RequestException;
use Checkdomain\Comodo\Model\Exception\UnknownApiException;
use Checkdomain\Comodo\Model\Exception\UnknownException;

use Checkdomain\Comodo\Model\Result\AutoApplyResult;
use Checkdomain\Comodo\Model\Result\GetDCVEMailAddressListResult;

use Zend\Mail\Storage\Folder;
use Zend\Mail\Storage\Message;


/**
 * Class Util
 * Provides functions to communicate with the Comodo API,requires an Account object, given to the Communication-adapter
 *
 * @package Checkdomain\Comodo
 */
class Util
{
    const COMODO_AUTO_APPLY_URL  = "https://secure.comodo.net/products/!AutoApplySSL";
    const COMODO_AUTO_REVOKE_URL = "https://secure.comodo.net/products/!AutoRevokeSSL";
    const COMODO_DCV_MAIL_URL    = "https://secure.comodo.net/products/!GetDCVEmailAddressList";
    const COMODO_DCV_RESEND_URL  = "https://secure.comodo.net/products/!ResendDCVEmail";

    const COMODO_DCV_CODE_URL = "https://secure.comodo.net/products/EnterDCVCode2";

    protected $communicationAdapter = null;
    protected $imapWithSearch       = null;
    protected $imapHelper           = null;

    /**
     * Constructs the Util with a communicationAdapter
     *
     * @param CommunicationAdapter $communicationAdapter
     */
    public function __construct(CommunicationAdapter $communicationAdapter = null, ImapWithSearch $imapWithSearch = null, ImapHelper $imapHelper = null)
    {
        $this->communicationAdapter = $communicationAdapter;
        $this->imapWithSearch = $imapWithSearch;
        $this->imapHelper = $imapHelper;
    }

    /*
     **
     * @param CommunicationAdapter $client
     *
     * @return Util
     */
    public function setCommunicationAdapter($communicationAdapter)
    {
        $this->communicationAdapter = $communicationAdapter;

        return $this;
    }

    /**
     * @return CommunicationAdapter
     */
    public function getCommunicationAdapter()
    {
        if ($this->communicationAdapter == null) {
            $this->communicationAdapter = new CommunicationAdapter();
        }

        return $this->communicationAdapter;
    }

    /**
     * @param ImapHelper $imapHelper
     *
     * @return Util
     */
    public function setImapHelper(ImapHelper $imapHelper)
    {
        $this->imapHelper = $imapHelper;

        return $this;
    }

    /**
     * @return ImapHelper
     */
    public function getImapHelper()
    {
        if ($this->imapHelper == null) {
            $this->imapHelper = new ImapHelper();
        }

        return $this->imapHelper;
    }

    /**
     * @param ImapWithSearch $imapWithSearch
     *
     * @return Util
     */
    public function setImapWithSearch(ImapWithSearch $imapWithSearch)
    {
        $this->imapWithSearch = $imapWithSearch;

        return $this;
    }

    /**
     * @return ImapWithSearch
     */
    public function getImapWithSearch()
    {
        return $this->imapWithSearch;
    }



    /**
     * Function apply for a certificate
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $params
     * @return AutoApplyResult
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\CSRException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function autoApplySSL($params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        // Send request
        $arr = $this->getCommunicationAdapter()->sendToApi(self::COMODO_AUTO_APPLY_URL, $params,
                                                           CommunicationAdapter::RESPONSE_URL_ENCODED);

        // Successful
        if ($arr["errorCode"] == 1 || $arr["errorCode"] == 0) {
            $result = new AutoApplyResult();

            if ($arr["errorCode"] == 0) {
                $paid = true;
            } else {
                $paid = false;
            }

            $result
                ->setPaid($paid)
                ->setCertificateID($arr["certificateID"])
                ->setExpectedDeliveryTime($arr["expectedDeliveryTime"])
                ->setOrderNumber($arr["orderNumber"])
                ->setTotalCost($arr["totalCost"]);

            return $result;
        } else {
            throw $this->createException($arr);
        }
    }

    /**
     * Function to revoke order
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $params
     * @return bool
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function autoRevokeSSL($params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        $responseArray = $this->getCommunicationAdapter()->sendToApi(self::COMODO_AUTO_REVOKE_URL, $params,
                                                                     CommunicationAdapter::RESPONSE_URL_ENCODED);

        if ($responseArray["errorCode"] == 0) {
            return true;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * Function to resend the DCV Email
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $params
     * @return bool
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function resendDCVEMail($params)
    {
        // Response is always url encoded
        $responseArray = $this->getCommunicationAdapter()->sendToApi(self::COMODO_DCV_RESEND_URL, $params,
                                                                     CommunicationAdapter::RESPONSE_URL_ENCODED);

        if ($responseArray["errorCode"] == 0) {
            return true;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * Function to get the DCV e-mail address-list
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $params
     * @return GetDCVEMailAddressListResult
     *
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function getDCVEMailAddressList($params)
    {
        // Response is always new line encoded
        $responseArray = $this->getCommunicationAdapter()->sendToApi(self::COMODO_DCV_MAIL_URL, $params,
                                                                     CommunicationAdapter::RESPONSE_NEW_LINE);

        if ($responseArray["errorCode"] == 0) {
            $result = new GetDCVEMailAddressListResult();

            $result
                ->setDomainName($responseArray["domain_name"])
                ->setWhoisEmail($responseArray["whois_email"])
                ->setLevel2Emails($responseArray["level2_email"])
                ->setLevel3Emails($responseArray["level3_email"]);

            return $result;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * Function to enter the DCV code, coming from DCV E-Mail
     *
     * @param $params
     * @return bool
     * @throws Model\Exception\UnknownException
     * @throws Model\Exception\ArgumentException
     */
    public function enterDCVCode($params)
    {
        // Check parameters
        if (!isset($params["dcvCode"])) {
            throw new ArgumentException(-3, 'Please provide an order-number', 'dcvCode', '');
        }

        if (!isset($params["orderNumber"])) {
            throw new ArgumentException(-3, 'Please provide an order-number', 'orderNumber', '');
        }

        // this is not a official request, so we need to use the website
        $responseString = $this->getCommunicationAdapter()->sendToWebsite(self::COMODO_DCV_CODE_URL, $params);

        // Decode answer from website
        if (stristr($responseString, "You have entered the correct Domain Control Validation code") != false) {
            return true;
        } else if (stristr($responseString, 'the certificate has already been issued') != false) {
            throw new ArgumentException(-104, 'The certificate has already been issued', 'certificate', $responseString);
        } else if (stristr($responseString, 'Invalid Validation Code!') != false) {
            throw new ArgumentException(-103, 'Invalid Validation Code', 'validation-code', $responseString);
        } else {
            throw new UnknownException(99, 'UnknownException', $responseString);
        }
    }

    /**
     * @param string $domainName
     * @param int    $orderNumber
     */
    public function getMails($domainName, $orderNumber = null)
    {
        if ($orderNumber != null) {
            $search = array(
                ' OR OR OR '.
                ' BODY "'.$orderNumber.'"'.
                ' SUBJECT "'.$orderNumber.'"'.
                ' BODY "'.$domainName.'"'.
                ' SUBJECT "'.$domainName.'"'
            );
        } else {
            $search = array(
                ' OR '.
                ' BODY "'.$domainName.'"'.
                ' SUBJECT "'.$domainName.'"'
            );
        }

        $subjects['order_received'] = 'Your order has been received';
        $subjects['information_required'] = 'Information Required: ';
        $subjects['confirmation'] = 'CONFIRMATION';
        $subjects['1_expiry'] = 'Customer certificate expiry warning (1 days)';
        $subjects['7_expiry'] = 'Customer certificate expiry warning (7 days)';
        $subjects['14_expiry'] = 'Customer certificate expiry warning (14 days)';
        $subjects['30_expiry'] = 'Customer certificate expiry warning (30 days)';
        $subjects['60_expiry'] = 'Customer certificate expiry warning (60 days)';

        $mails = $this->getImapHelper()->fetchMails($this->getImapWithSearch(), array(), $search);

        foreach($mails as $i => $mail) {
            $mails[$i]['status'] = null;

            foreach($subjects as $key => $subject) {
                if(stristr($mail['subject'], $subject) !== false) {
                    $mails[$i]['status'] = $key;
                    break;
                }
            }
        }

        return $mails;
    }

    /**
     * Function to create an exception for API errorcodes
     *
     * @param $responseArray
     * @return AccountException|ArgumentException|CSRException|RequestException|UnknownApiException|UnknownException
     */
    protected function createException($responseArray)
    {
        $className = null;

        switch ($responseArray["errorCode"]) {
            case -1:  // Not using https:
            case -17: // Wrong HTTP-method
                return new RequestException($responseArray["errorCode"],
                                            $responseArray["errorMessage"],
                                            $responseArray["responseString"]);
                break;

            case -2: // unrecognized argument
            case -3: // missing argument
            case -4: // invalid argument
            case -7: // invalid ISO Country
            case -18: // Name = Fully-Qualified Domain Name
            case -35: // Name = = IP
            case -19: // Name = = Accessible IP
                return new ArgumentException($responseArray["errorCode"],
                                             $responseArray["errorMessage"],
                                             $responseArray["errorItem"],
                                             $responseArray["responseString"]);
                break;

            case -16: // Permission denied
            case -15: // insufficient credits
                return new AccountException($responseArray["errorCode"],
                                            $responseArray["errorMessage"],
                                            $responseArray["responseString"]);

            case -5: // contains wildcard
            case -6: // no wildcard, but must have
            case -8: // missing field
            case -9: // base64 decode exception
            case -10: // decode exception
            case -11: // unsupported algorithm
            case -12: // invalid signature
            case -13: // unsupported key size
            case -20: // Already rejected
            case -21: // Already revoked
            case -26: // current being issued
            case -40: // key compromised
                return new CSRException($responseArray["errorCode"],
                                        $responseArray["errorMessage"],
                                        $responseArray["responseString"]);
                break;

            case -14:
                return new UnknownApiException($responseArray["errorCode"],
                                               $responseArray["errorMessage"],
                                               $responseArray["responseString"]);
                break;

            default:
                return new UnknownException($responseArray["errorCode"],
                                            $responseArray["errorMessage"],
                                            $responseArray["responseString"]);
                break;
        }
    }
}