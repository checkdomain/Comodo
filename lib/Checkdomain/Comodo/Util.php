<?php
namespace Checkdomain\Comodo;

use Checkdomain\Comodo\Model\Exception\AccountException;
use Checkdomain\Comodo\Model\Exception\ArgumentException;
use Checkdomain\Comodo\Model\Exception\CSRException;
use Checkdomain\Comodo\Model\Exception\RequestException;
use Checkdomain\Comodo\Model\Exception\UnknownApiException;
use Checkdomain\Comodo\Model\Exception\UnknownException;

use Checkdomain\Comodo\Model\Result\AutoApplyResult;
use Checkdomain\Comodo\Model\Result\AutoReplaceResult;
use Checkdomain\Comodo\Model\Result\CollectSslResult;
use Checkdomain\Comodo\Model\Result\GetDCVEMailAddressListResult;
use Checkdomain\Comodo\Model\Result\GetMDCDomainDetailsResult;

/**
 * Class Util
 * Provides functions to communicate with the Comodo API,requires an Account object, given to the Communication-adapter
 *
 * @package Checkdomain\Comodo
 */
class Util
{
    const COMODO_AUTO_APPLY_URL         = "https://secure.comodo.net/products/!AutoApplySSL";
    const COMODO_AUTO_REVOKE_URL        = "https://secure.comodo.net/products/!AutoRevokeSSL";
    const COMODO_DCV_MAIL_URL           = "https://secure.comodo.net/products/!GetDCVEmailAddressList";
    const COMODO_DCV_RESEND_URL         = "https://secure.comodo.net/products/!ResendDCVEmail";
    const COMODO_AUTO_UPDATE_DCV_URL    = "https://secure.comodo.net/products/!AutoUpdateDCV";
    const COMODO_PROVIDE_EV_DETAILS_URL = "https://secure.comodo.net/products/!ProvideEVDetails";
    const COMODO_MDC_DOMAIN_DETAILS_URL = "https://secure.comodo.net/products/!GetMDCDomainDetails";
    const COMODO_AUTO_REPLACE_URL       = "https://secure.comodo.net/products/!AutoReplaceSSL";
    const COMODO_COLLECT_SSL_URL        = "https://secure.comodo.net/products/download/CollectSSL";

    const COMODO_DCV_CODE_URL = "https://secure.comodo.net/products/EnterDCVCode2";

    protected $communicationAdapter = null;
    protected $imapWithSearch = null;
    protected $imapHelper = null;

    /**
     * Constructs the Util with a communicationAdapter
     *
     * @param CommunicationAdapter|null $communicationAdapter
     * @param ImapWithSearch|null       $imapWithSearch
     * @param ImapHelper|null           $imapHelper
     */
    public function __construct(CommunicationAdapter $communicationAdapter, ImapWithSearch $imapWithSearch, ImapHelper $imapHelper)
    {
        $this->communicationAdapter = $communicationAdapter;
        $this->imapWithSearch       = $imapWithSearch;
        $this->imapHelper           = $imapHelper;
    }

    /**
     * Function apply for a certificate
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param array $params
     *
     * @return AutoApplyResult
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\CSRException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function autoApplySSL(array $params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        // Send request
        $arr = $this->communicationAdapter
                    ->sendToApi(self::COMODO_AUTO_APPLY_URL, $params, CommunicationAdapter::RESPONSE_URL_ENCODED);

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
                ->setTotalCost($arr["totalCost"])
                ->setRequestQuery($arr['requestQuery']);

            return $result;
        } else {
            throw $this->createException($arr);
        }
    }

    /**
     * Function update for a certificate
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param array $params
     *
     * @return AutoApplyResult
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\CSRException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function autoReplaceSSL(array $params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        // Send request
        $arr = $this->communicationAdapter
            ->sendToApi(self::COMODO_AUTO_REPLACE_URL, $params, CommunicationAdapter::RESPONSE_URL_ENCODED);

        // Successful
        if ($arr["errorCode"] == 0) {
            $result = new AutoReplaceResult();

            $result
                ->setCertificateID($arr["certificateID"])
                ->setExpectedDeliveryTime($arr["expectedDeliveryTime"]);

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
     * @param array $params
     *
     * @return bool
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function autoRevokeSSL(array $params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        return $this->sendBooleanRequest(self::COMODO_AUTO_REVOKE_URL,
                                         $params,
                                         CommunicationAdapter::RESPONSE_URL_ENCODED
        );
    }

    /**
     * Function to auto update dcv
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param array $params
     *
     * @return bool
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function autoUpdateDCV(array $params)
    {
        return $this->sendBooleanRequest(self::COMODO_AUTO_UPDATE_DCV_URL,
                                         $params,
                                         CommunicationAdapter::RESPONSE_URL_ENCODED
        );
    }

    /**
     * Function to get details of a certificate
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param array $params
     *
     * @return CollectSslResult
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\CSRException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function collectSsl(array $params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        // Send request
        $arr = $this->communicationAdapter
            ->sendToApi(self::COMODO_COLLECT_SSL_URL, $params, CommunicationAdapter::RESPONSE_URL_ENCODED);

        // Successful
        if ($arr["errorCode"] >= 0) {
            $result = new CollectSslResult();

            foreach($arr as $key => $value) {
                if($key == 'notBefore' || $key == 'notAfter') {
                    $value = new \DateTime('@' . $value);
                }

                $function = 'set' . ucfirst($key);

                // For example setErrorCode does not exists, so check before
                if(method_exists($result, $function)) {

                    call_user_func(array($result, $function), $value);
                }
            }

            return $result;
        } else {
            throw $this->createException($arr);
        }
    }



    /**
     * Function to resend the DCV Email
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param array $params
     *
     * @return bool
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function resendDCVEMail(array $params)
    {
        return $this->sendBooleanRequest(self::COMODO_DCV_RESEND_URL,
                                         $params,
                                         CommunicationAdapter::RESPONSE_URL_ENCODED
        );
    }

    /**
     * @param array $params
     *
     * @return bool
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\CSRException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function provideEVDetails(array $params)
    {
        return $this->sendBooleanRequest(self::COMODO_PROVIDE_EV_DETAILS_URL,
                                         $params,
                                         CommunicationAdapter::RESPONSE_URL_ENCODED
        );
    }

    /**
     * Function to get the DCV e-mail address-list
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param array $params
     *
     * @return GetDCVEMailAddressListResult
     *
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function getDCVEMailAddressList(array $params)
    {
        // Response is always new line encoded
        $responseArray = $this->communicationAdapter
                              ->sendToApi(
                                  self::COMODO_DCV_MAIL_URL,
                                  $params,
                                  CommunicationAdapter::RESPONSE_NEW_LINE
                              );

        if ($responseArray["errorCode"] == 0) {
            $result = new GetDCVEMailAddressListResult();

            $result
                ->setDomainName($responseArray["domain_name"])
                ->setWhoisEmail($responseArray["whois_email"])
                ->setLevel2Emails($responseArray["level2_email"])
                ->setLevel3Emails($responseArray["level3_email"])
                ->setRequestQuery($responseArray['requestQuery']);

            return $result;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * Function to get details of a order-number (this API support just one domain)
     *
     * https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/GetMDCDomainDetails%20v1.00.pdf
     *
     * @param array $params
     *
     * @return GetMDCDomainDetailsResult
     *
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\CSRException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function getMDCDomainDetails(array $params)
    {
        // Response is always new line encoded
        $responseArray = $this->communicationAdapter
                              ->sendToApi(
                                  self::COMODO_MDC_DOMAIN_DETAILS_URL,
                                  $params,
                                  CommunicationAdapter::RESPONSE_URL_ENCODED
                              );

        if ($responseArray["errorCode"] == 0) {
            $result = new GetMDCDomainDetailsResult();

            $result
                ->setDomainName($responseArray["1_domainName"])
                ->setDcvMethod($responseArray["1_dcvMethod"])
                ->setDcvStatus($responseArray["1_dcvStatus"]);

            return $result;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * Function to enter the DCV code, coming from DCV E-Mail
     *
     * @param array $params
     *
     * @return bool
     * @throws Model\Exception\UnknownException
     * @throws Model\Exception\ArgumentException
     */
    public function enterDCVCode(array $params)
    {
        // Check parameters
        if (!isset($params["dcvCode"])) {
            throw new ArgumentException(-3, 'Please provide an order-number', 'dcvCode', '');
        }

        if (!isset($params["orderNumber"])) {
            throw new ArgumentException(-3, 'Please provide an order-number', 'orderNumber', '');
        }

        // this is not a official request, so we need to use the website
        $responseString = $this->communicationAdapter->sendToWebsite(self::COMODO_DCV_CODE_URL, $params);

        // Decode answer from website
        if (stristr($responseString, "You have entered the correct Domain Control Validation code") !== false) {
            return true;
        } else if (stristr($responseString, 'the certificate has already been issued') !== false) {
            throw new ArgumentException(-104, 'The certificate has already been issued', 'certificate', $responseString);
        } else if (stristr($responseString, 'Invalid Validation Code!') !== false) {
            throw new ArgumentException(-103, 'Invalid Validation Code', 'validation-code', $responseString);
        } else {
            throw new UnknownException(99, 'UnknownException', $responseString);
        }
    }

    /**
     * @param string $url
     * @param array  $params
     * @param int    $type
     *
     * @return bool
     * @throws AccountException
     * @throws ArgumentException
     * @throws CSRException
     * @throws RequestException
     * @throws UnknownApiException
     * @throws UnknownException
     */
    protected function sendBooleanRequest($url, array $params, $type)
    {
        // Response is always url encoded
        $responseArray = $this->communicationAdapter
            ->sendToApi(
                $url,
                $params,
                $type
            );

        if ($responseArray["errorCode"] == 0) {
            return true;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * @param string   $domainName
     * @param null     $orderNumbers
     * @param \Closure $callbackFunction
     *
     * @return array
     */
    public function getMails($domainName, $orderNumbers = null, \Closure $callbackFunction = null)
    {
        $orList    = ' OR ';
        $whereList = ' BODY "' . $domainName . '"';
        $whereList .= ' SUBJECT "' . $domainName . '"';

        if (is_array($orderNumbers)) {
            foreach ($orderNumbers as $orderNumber) {
                $orList .= ' OR OR ';
                $whereList .= ' BODY "' . $orderNumber . '"';
                $whereList .= ' SUBJECT "' . $orderNumber . '"';
            }
        }

        $search = $orList . " " . $whereList;

        return $this->imapHelper
                    ->fetchMails($this->imapWithSearch, array(), $search, null, false, false, $callbackFunction);
    }

    /**
     * @param bool     $markProcessed
     * @param \Closure $callbackFunction
     *
     * @return array
     */
    public function getUnprocessedMails($markProcessed = true, \Closure $callbackFunction = null)
    {
        $search = ' NOT KEYWORD "' . ImapHelper::PROCESSED_FLAG . '"';

        return $this->imapHelper
                    ->fetchMails(
                        $this->imapWithSearch,
                        array(),
                        $search,
                        null,
                        $markProcessed,
                        true,
                        $callbackFunction
                    );
    }

    /**
     * Function to create an exception for API errorcodes
     *
     * @param array $responseArray
     *
     * @return AccountException|ArgumentException|CSRException|RequestException|UnknownApiException|UnknownException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function createException(array $responseArray)
    {
        switch ($responseArray["errorCode"]) {
            case -1: // Not using https:
            case -17: // Wrong HTTP-method
                return new RequestException(
                    $responseArray["errorCode"],
                    $responseArray["errorMessage"],
                    $responseArray["responseString"]
                );

            case -2: // unrecognized argument
            case -3: // missing argument
            case -4: // invalid argument
            case -7: // invalid ISO Country
            case -18: // Name = Fully-Qualified Domain Name
            case -35: // Name = = IP
            case -19: // Name = = Accessible IP
                return new ArgumentException(
                    $responseArray["errorCode"],
                    $responseArray["errorMessage"],
                    $responseArray["errorItem"],
                    $responseArray["responseString"]
                );

            case -16: // Permission denied
            case -15: // insufficient credits
                return new AccountException(
                    $responseArray["errorCode"],
                    $responseArray["errorMessage"],
                    $responseArray["responseString"]
                );

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
                return new CSRException(
                    $responseArray["errorCode"],
                    $responseArray["errorMessage"],
                    $responseArray["responseString"]
                );

            case -14:
                return new UnknownApiException(
                    $responseArray["errorCode"],
                    $responseArray["errorMessage"],
                    $responseArray["responseString"]
                );

            default:
                return new UnknownException(
                    $responseArray["errorCode"],
                    $responseArray["errorMessage"],
                    $responseArray["responseString"]
                );
        }
    }
}
