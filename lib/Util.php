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
use Checkdomain\Comodo\Model\Result\UpdateUserEvClickThroughResult;
use Checkdomain\Comodo\Model\Result\SslCheckerResult;
use Checkdomain\Comodo\Model\Result\WebHostReportResult;

/**
 * Class Util
 * Provides functions to communicate with the Comodo API,requires an Account object, given to the Communication-adapter
 */
class Util
{
    const COMODO_AUTO_APPLY_URL = 'https://secure.comodo.net/products/!AutoApplySSL';
    const COMODO_AUTO_REVOKE_URL = 'https://secure.comodo.net/products/!AutoRevokeSSL';
    const COMODO_DCV_MAIL_URL = 'https://secure.comodo.net/products/!GetDCVEmailAddressList';
    const COMODO_DCV_RESEND_URL = 'https://secure.comodo.net/products/!ResendDCVEmail';
    const COMODO_AUTO_UPDATE_DCV_URL = 'https://secure.comodo.net/products/!AutoUpdateDCV';
    const COMODO_PROVIDE_EV_DETAILS_URL = 'https://secure.comodo.net/products/!ProvideEVDetails';
    const COMODO_MDC_DOMAIN_DETAILS_URL = 'https://secure.comodo.net/products/!GetMDCDomainDetails';
    const COMODO_AUTO_REPLACE_URL = 'https://secure.comodo.net/products/!AutoReplaceSSL';
    const COMODO_COLLECT_SSL_URL = 'https://secure.comodo.net/products/download/CollectSSL';
    const COMODO_UPDATE_USER_EV_CLICK_THROUGH = 'https://secure.comodo.net/products/!UpdateUserEvClickThrough';
    const COMODO_WEB_HOST_REPORT = 'https://secure.comodo.net/products/!WebHostReport';
    const COMODO_SSLCHECKER = 'https://secure.comodo.com/sslchecker';

    const COMODO_DCV_CODE_URL = 'https://secure.comodo.net/products/EnterDCVCode2';

    /**
     * @var CommunicationAdapter
     */
    protected $communicationAdapter;

    /**
     * @var ImapAdapter
     */
    protected $imapAdapter;

    /**
     * @var ImapHelper
     */
    protected $imapHelper;

    /**
     * Constructs the Util with a communicationAdapter
     *
     * @param CommunicationAdapter|null $communicationAdapter
     * @param ImapAdapter|null $imapAdapter
     * @param ImapHelper|null $imapHelper
     */
    public function __construct(CommunicationAdapter $communicationAdapter, ImapAdapter $imapAdapter, ImapHelper $imapHelper)
    {
        $this->communicationAdapter = $communicationAdapter;
        $this->imapAdapter = $imapAdapter;
        $this->imapHelper = $imapHelper;
    }


    /**
     * @return CommunicationAdapter
     */
    public function getCommunicationAdapter()
    {
        return $this->communicationAdapter;
    }

    /**
     * @param CommunicationAdapter $communicationAdapter
     *
     * @return Util
     */
    public function setCommunicationAdapter(CommunicationAdapter $communicationAdapter)
    {
        $this->communicationAdapter = $communicationAdapter;

        return $this;
    }

    /**
     * @return ImapHelper
     */
    public function getImapHelper()
    {
        return $this->imapHelper;
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
     * @return ImapAdapter
     */
    public function getImapAdapter()
    {
        return $this->imapAdapter;
    }

    /**
     * @param ImapAdapter $imapAdapter
     *
     * @return Util
     */
    public function setImapAdapter(ImapAdapter $imapAdapter)
    {
        $this->imapAdapter = $imapAdapter;

        return $this;
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
        $params['responseFormat'] = CommunicationAdapter::RESPONSE_URL_ENCODED;
        $params['showCertificateID'] = 'Y';

        // Send request
        $arr = $this
            ->communicationAdapter
            ->sendToApi(self::COMODO_AUTO_APPLY_URL, $params, CommunicationAdapter::RESPONSE_URL_ENCODED);

        // Successful
        if ($arr['errorCode'] == 1 || $arr['errorCode'] == 0) {
            $result = new AutoApplyResult();

            if ($arr['errorCode'] == 0) {
                $paid = true;
            } else {
                $paid = false;
            }

            $result
                ->setPaid($paid)
                ->setCertificateID($arr['certificateID'])
                ->setExpectedDeliveryTime($arr['expectedDeliveryTime'])
                ->setOrderNumber($arr['orderNumber'])
                ->setTotalCost($arr['totalCost'])
                ->setRequestQuery($arr['requestQuery']);

            return $result;
        } else {
            throw $this->createException($arr);
        }
    }

    /**
     * @param array $params
     *
     * @return UpdateUserEvClickThroughResult
     * @throws AccountException
     * @throws ArgumentException
     * @throws CSRException
     * @throws RequestException
     * @throws UnknownApiException
     * @throws UnknownException
     */
    public function updateUserEvClickThrough(array $params)
    {
        // Send request
        $arr = $this
            ->communicationAdapter
            ->sendToApi(
                self::COMODO_UPDATE_USER_EV_CLICK_THROUGH,
                $params,
                CommunicationAdapter::RESPONSE_URL_ENCODED
            );

        // Successful
        if ($arr['errorCode'] == 0) {
            $result = new UpdateUserEvClickThroughResult();

            $result
                ->setStatus($arr['status']);

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
        $params['responseFormat'] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        // Send request
        $arr = $this
            ->communicationAdapter
            ->sendToApi(
                self::COMODO_AUTO_REPLACE_URL,
                $params,
                CommunicationAdapter::RESPONSE_URL_ENCODED
            );

        // Successful
        if ($arr['errorCode'] == 0) {
            $result = new AutoReplaceResult();

            $result
                ->setCertificateID($arr['certificateID'])
                ->setExpectedDeliveryTime($arr['expectedDeliveryTime']);

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
        $params['responseFormat'] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        return $this->sendBooleanRequest(
            self::COMODO_AUTO_REVOKE_URL,
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
        return $this->sendBooleanRequest(
            self::COMODO_AUTO_UPDATE_DCV_URL,
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
        // Not decode the following indexes
        $notDecode = array('caCertificate', 'certificate', 'netscapeCertificateSequence', 'zipFile');

        // Force threating as array
        $forceArray = array('caCertificate');

        // Two choices, we want url-encoded
        $params['responseFormat'] = CommunicationAdapter::RESPONSE_URL_ENCODED;

        // Send request
        $arr = $this
            ->communicationAdapter
            ->sendToApi(
                self::COMODO_COLLECT_SSL_URL,
                $params,
                CommunicationAdapter::RESPONSE_URL_ENCODED,
                $notDecode,
                $forceArray
            );

        // Successful
        if ($arr['errorCode'] >= 0) {
            $result = new CollectSslResult();

            $this->fill($result, $arr, array('notBefore', 'notAfter'));

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
        return $this
            ->sendBooleanRequest(
                self::COMODO_DCV_RESEND_URL,
                $params,
                CommunicationAdapter::RESPONSE_URL_ENCODED
            );
    }

    /**
     * @param array $params
     *
     * @deprecated Comodo support told this function doesn't have any effect anymore
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
        return $this->sendBooleanRequest(
            self::COMODO_PROVIDE_EV_DETAILS_URL,
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
     * @throws AccountException
     * @throws ArgumentException
     * @throws CSRException
     * @throws RequestException
     * @throws UnknownApiException
     * @throws UnknownException
     */
    public function getDCVEMailAddressList(array $params)
    {
        // Force threating as array
        $forceArray = array('whois_email', 'level2_email', 'level3_email');

        // Response is always new line encoded
        $responseArray = $this
            ->communicationAdapter
            ->sendToApi(
                self::COMODO_DCV_MAIL_URL,
                $params,
                CommunicationAdapter::RESPONSE_NEW_LINE,
                null,
                $forceArray
            );

        if ($responseArray['errorCode'] == 0) {
            $result = new GetDCVEMailAddressListResult();

            $result
                ->setDomainName($responseArray['domain_name'])
                ->setWhoisEmail($responseArray['whois_email'])
                ->setLevel2Emails($responseArray['level2_email'])
                ->setRequestQuery($responseArray['requestQuery']);

            if (isset($responseArray['level3_email'])) {
                $result->setLevel3Emails($responseArray['level3_email']);
            }

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
        $responseArray = $this
            ->communicationAdapter
            ->sendToApi(
                self::COMODO_MDC_DOMAIN_DETAILS_URL,
                $params,
                CommunicationAdapter::RESPONSE_URL_ENCODED
            );

        if ($responseArray['errorCode'] == 0) {
            $result = new GetMDCDomainDetailsResult();

            $result
                ->setDomainName($responseArray['1_domainName'])
                ->setDcvMethod($responseArray['1_dcvMethod'])
                ->setDcvStatus($responseArray['1_dcvStatus']);

            return $result;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * Function to do a sslcheck
     *
     * https://secure.comodo.net/api/pdf/latest/SSLChecker.pdf
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
    public function sslChecker(array $params)
    {
        // Response is always new line encoded
        $responseArray = $this
            ->communicationAdapter
            ->sendToApi(
                self::COMODO_SSLCHECKER,
                $params,
                CommunicationAdapter::RESPONSE_URL_ENCODED
            );

        if ($responseArray['error_code'] == 0) {
            $result = new SslCheckerResult();

            $result
                ->setServerUrl($responseArray['server_url'])
                ->setServerDomainIsIDN($responseArray['server_domain_isIDN'])
                ->setServerDomainUtf8($responseArray['server_domain_utf8'])
                ->setServerDomainAce($responseArray['server_domain_ace'])
                ->setServerIp($responseArray['server_ip'])
                ->setServerPort($responseArray['server_port'])
                ->setServerSoftware($responseArray['server_software'])
                ->setCertNotBeforeFromUnixTimestamp($responseArray['cert_notBefore'])
                ->setCertNotAfterFromUnixTimestamp($responseArray['cert_notAfter'])
                ->setCertValidityNotBefore($responseArray['cert_validity_notBefore'])
                ->setCertValidityNotAfter($responseArray['cert_validity_notAfter'])
                ->setCertKeyAlgorithm($responseArray['cert_key_algorithm'])
                ->setCertKeySize($responseArray['cert_key_size'])
                ->setCertSubjectCN($responseArray['cert_subject_DN'])
                ->setCertSubjectCN($responseArray['cert_subject_CN'])
                ->setCertSubjectOU($responseArray['cert_subject_OU'])
                ->setCertSubjectOrganization($responseArray['cert_subject_O'])
                ->setCertSubjectStreetAddress1($responseArray['cert_subject_streetAddress_1'])
                ->setCertSubjectStreetAddress2($responseArray['cert_subject_streetAddress_2'])
                ->setCertSubjectStreetAddress3($responseArray['cert_subject_streetAddress_3'])
                ->setCertSubjectLocality($responseArray['cert_subject_L'])
                ->setCertSubjectState($responseArray['cert_subject_S'])
                ->setCertSubjectPostalCode($responseArray['cert_subject_postalCode'])
                ->setCertSubjectCountry($responseArray['cert_subject_C'])
                ->setCertIsMultiDomain($responseArray['cert_isMultiDomain'])
                ->setCertIsWildcard($responseArray['cert_isWildcard'])
                ->setCertIssuerDN($responseArray['cert_issuer_DN'])
                ->setCertIssuerCN($responseArray['cert_issuer_CN'])
                ->setCertIssuerOrganization($responseArray['cert_issuer_O'])
                ->setCertIssuerOrganization($responseArray['cert_issuer_C'])
                ->setCertIssuerBrand($responseArray['cert_issuer_brand'])
                ->setCertPolicyOID($responseArray['cert_policyOID'])
                ->setCertValidation($responseArray['cert_validation']);

            return $result;
        } else {
            throw $this->createException($responseArray);
        }
    }

    /**
     * Function to call WebHostReport api
     *
     * https://secure.comodo.net/products/!WebHostReport
     *
     * Details for params usage:
     * https://secure.comodo.net/api/pdf/latest/WebHostReport.pdf
     *
     * @param array $params
     *
     * @return WebHostReportResult
     *
     * @throws Model\Exception\AccountException
     * @throws Model\Exception\ArgumentException
     * @throws Model\Exception\CSRException
     * @throws Model\Exception\RequestException
     * @throws Model\Exception\UnknownApiException
     * @throws Model\Exception\UnknownException
     */
    public function webHostReport(array $params) {

        if (empty($params['lastResultNo'])) {
            $params['lastResultNo'] = 10;
        }

        // Response is always new line encoded
        $responseArray = $this
            ->communicationAdapter
            ->sendToApi(
                self::COMODO_WEB_HOST_REPORT,
                $params,
                CommunicationAdapter::RESPONSE_URL_ENCODED
            );
        if ($responseArray['error_code'] == 0) {
            $result = new WebHostReportResult();
            $result->importEntries($responseArray);
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
        if (!isset($params['dcvCode'])) {
            throw new ArgumentException(-3, 'Please provide an order-number', 'dcvCode', '');
        }

        if (!isset($params['orderNumber'])) {
            throw new ArgumentException(-3, 'Please provide an order-number', 'orderNumber', '');
        }

        // this is not a official request, so we need to use the website
        $responseString = $this
            ->communicationAdapter
            ->sendToWebsite(self::COMODO_DCV_CODE_URL, $params);

        // Decode answer from website
        if (stristr($responseString, 'You have entered the correct Domain Control Validation code') !== false) {
            return true;
        } elseif (stristr($responseString, 'the certificate has already been issued') !== false) {
            throw new ArgumentException(-104, 'The certificate has already been issued', 'certificate', $responseString);
        } elseif (stristr($responseString, 'Invalid Validation Code!') !== false) {
            throw new ArgumentException(-103, 'Invalid Validation Code', 'validation-code', $responseString);
        } else {
            throw new UnknownException(99, 'UnknownException', $responseString);
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
        $whereList = ' BODY "'.$domainName.'"';
        $whereList .= ' SUBJECT "'.$domainName.'"';

        if (is_array($orderNumbers)) {
            foreach ($orderNumbers as $orderNumber) {
                $orList .= ' OR OR ';
                $whereList .= ' BODY "'.$orderNumber.'"';
                $whereList .= ' SUBJECT "'.$orderNumber.'"';
            }
        }

        $search = $orList.' '.$whereList;

        return $this
            ->imapHelper
            ->fetchMails($this->imapAdapter, $search, false, false, $callbackFunction);
    }

    /**
     * @param bool     $markProcessed
     * @param \Closure $callbackFunction
     *
     * @return array
     */
    public function getUnprocessedMails($markProcessed = true, \Closure $callbackFunction = null)
    {
        $search = ' NOT KEYWORD "'.ImapHelper::PROCESSED_FLAG.'"';

        return $this
            ->imapHelper
            ->fetchMails(
                $this->imapAdapter,
                $search,
                $markProcessed,
                true,
                $callbackFunction
            );
    }

    /**
     * Function to create an exception for API errorcodes
     *
     * @param array|mixed $responseArray
     *
     * @return AccountException|ArgumentException|CSRException|RequestException|UnknownApiException|UnknownException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function createException($responseArray)
    {
        if (is_array($responseArray) === false) {
            return new UnknownException(
                0,
                'Internal error',
                $responseArray['responseString']
            );
        }

        switch ($responseArray['errorCode']) {
            case -1: // Not using https:
            case -17: // Wrong HTTP-method
                return new RequestException(
                    $responseArray['errorCode'],
                    $responseArray['errorMessage'],
                    $responseArray['responseString']
                );

            case -2: // unrecognized argument
            case -3: // missing argument
            case -4: // invalid argument
            case -7: // invalid ISO Country
            case -18: // Name = Fully-Qualified Domain Name
            case -35: // Name = = IP
            case -19: // Name = = Accessible IP
                return new ArgumentException(
                    $responseArray['errorCode'],
                    $responseArray['errorMessage'],
                    ((isset($responseArray['errorItem'])) ? $responseArray['errorItem'] : null),
                    $responseArray['responseString']
                );

            case -16: // Permission denied
            case -15: // insufficient credits
                return new AccountException(
                    $responseArray['errorCode'],
                    $responseArray['errorMessage'],
                    $responseArray['responseString']
                );

            case -5: // contains wildcard
            case -6: // no wildcard, but must have
            case -8: // missing field
            case -9: // base64 decode exception
            case -10: // decode exception
            case -11: // unsupported algorithm
            case -12: // invalid signature
            case -13: // unsupported key size
            case -20: // Already rejected / Order relevated
            case -21: // Already revoked
            case -26: // current being issued
            case -40: // key compromised
                return new CSRException(
                    $responseArray['errorCode'],
                    $responseArray['errorMessage'],
                    $responseArray['responseString']
                );

            case -14:
                return new UnknownApiException(
                    $responseArray['errorCode'],
                    $responseArray['errorMessage'],
                    $responseArray['responseString']
                );

            default:
                return new UnknownException(
                    $responseArray['errorCode'],
                    $responseArray['errorMessage'],
                    $responseArray['responseString']
                );
        }
    }

    /**
     * @param CollectSslResult $object
     * @param array            $arr
     * @param array            $timestampFields
     *
     * @return $this
     */
    protected function fill(CollectSslResult $object, array $arr, array $timestampFields = array())
    {
        foreach ($arr as $key => $value) {
            if (in_array($key, $timestampFields)) {
                $value = new \DateTime('@'.$value);
            }

            $function = 'set'.ucfirst($key);

            // For example setErrorCode does not exists, so check before
            if (method_exists($object, $function)) {
                call_user_func(array($object, $function), $value);
            }
        }

        return $this;
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
        $responseArray = $this
            ->communicationAdapter
            ->sendToApi(
                $url,
                $params,
                $type
            );

        if ($responseArray['errorCode'] == 0) {
            return true;
        } else {
            throw $this->createException($responseArray);
        }
    }
}
