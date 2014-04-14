<?php
namespace Checkdomain\Comodo;

use Checkdomain\Comodo\Model\Account;
use Guzzle\Http\Client;

class Util
{
    const COMODO_AUTOAPPLY_URL  = "https://secure.comodo.net/products/!AutoApplySSL";
    const COMODO_AUTOREVOKE_URL = "https://secure.comodo.net/products/!AutoRevokeSSL";
    const COMODO_DCV_MAIL_URL   = "https://secure.comodo.net/products/!GetDCVEmailAddressList";
    const COMODO_DCV_RESEND_URL = "https://secure.comodo.net/products/!ResendDCVEmail";

    const COMODO_DCV_CODE_URL = "https://secure.comodo.net/products/EnterDCVCode2";

    const RESPONSE_NEW_LINE    = 0;
    const RESPONSE_URL_ENCODED = 1;

    /**
     * @var Account
     */
    protected $account;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param \Checkdomain\Comodo\Model\Account $account
     *
     * @return Util
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return \Checkdomain\Comodo\Model\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param \Guzzle\Http\Client $client
     *
     * @return Util
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \Guzzle\Http\Client
     */
    public function getClient()
    {
        if ($this->client == null) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Function apply for a certificate
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $param
     *
     * @return array
     */
    public function autoApplySSL($params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = self::RESPONSE_URL_ENCODED;

        return $this->sendToApi(self::COMODO_AUTOAPPLY_URL, $params, self::RESPONSE_URL_ENCODED);
    }

    /**
     * Function to revoke order
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $param
     *
     * @return array
     */
    public function autoRevokeSSL($params)
    {
        // Two choices, we want url-encoded
        $params["responseFormat"] = self::RESPONSE_URL_ENCODED;

        return $this->sendToApi(self::COMODO_AUTOREVOKE_URL, $params, self::RESPONSE_URL_ENCODED);
    }

    /**
     * Function to resend the DCV mail
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $param
     *
     * @return array
     */
    public function resendDCVMail($params)
    {
        // Response is always url encoded
        return $this->sendToApi(self::COMODO_DCV_RESEND_URL, $params, self::RESPONSE_URL_ENCODED);
    }

    /**
     * Function to enter to get the DCV mail adresses
     *
     * See documentation of params at https://secure.comodo.net/api/pdf/webhostreseller/sslcertificates/
     *
     * @param $param
     *
     * @return array
     */
    public function getDCVMailAddresses($params)
    {
        // Response is always new line encoded
        return $this->sendToApi(self::COMODO_DCV_MAIL_URL, $params, self::RESPONSE_NEW_LINE);
    }

    /**
     * Function to enter the DCV code, coming from DCV E-Mail
     *
     * @param $param
     *
     * @return array
     */
    public function enterDCVCode($params)
    {
        $return = array();

        // Check parameters
        if (!isset($params["dcvCode"])) {
            $return["errorcode"]    = -3;
            $return["errorMessage"] = "Please provide a DVC code";

            return $return;
        }

        if (!isset($params["orderNumber"])) {
            $return["errorcode"]   = -3;
            $return["orderNumber"] = "Please provide an order-number";

            return $return;
        }

        // this is not a official request, so we need to use the website
        $responseString = $this->sendToWebsite(self::COMODO_DCV_CODE_URL, $params);

        // Decode answer from website
        if (stristr($responseString, "You have entered the correct Domain Control Validation code") != false) {
            $return["errorcode"]        = 0;
        } else if (stristr($responseString, "the certificate has already been issued") != false) {
            $return["errorcode"]        = 4;
            $return["errorMessage"]     = "The certificate has already been issued";
        } else if (stristr($responseString, "Invalid Validation Code!") != false) {
            $return["errorcode"]        = 3;
            $return["errorMessage"]     = "Invalid Validation Code";
        } else {
            $return["errorcode"]        = 99;
            $return["errorMessage"]     = "Unknown error";
            $return["originalResponse"] = $responseString;
        }

        return $return;
    }

    /**
     * Sends a query to the provided url and return the response body.
     *
     * @param $url
     * @param $params
     *
     * @return string
     */
    protected function sendToWebsite($url, $params)
    {
        $url_encoded = http_build_query($params);

        // Sending request
        $client  = $this->getClient();
        $request = $client->post($url, null, $url_encoded);

        $response = $request->send();

        // Getting response body
        $responseString = $response->getBody(true);
        $responseString = trim($responseString);

        return $responseString;
    }

    /**
     * Send a request to the comodo API, and decodes the response as given
     *
     * @param $url
     * @param $params
     * @param int $responseType
     *
     * @return array|bool
     */
    protected function sendToApi($url, $params, $responseType = self::RESPONSE_NEW_LINE)
    {
        if (!$this->preSendToApiCheck()) {
            return false;
        }

        // Merging post-data
        $fields                  = array();
        $fields["loginName"]     = $this->getAccount()->getLoginName();
        $fields["loginPassword"] = $this->getAccount()->getLoginPassword();
        $fields                  = array_merge($fields, $params);

        // Sending request
        $client   = $this->getClient();
        $request  = $client->post($url, null, $fields);
        $response = $request->send();

        // Getting response body
        $responseString = $response->getBody(true);
        $responseString = trim($responseString);

        // Decoding and returning response
        if ($responseType == self::RESPONSE_NEW_LINE) {
            return $this->decodeNewLineEncodedResponse($responseString);
        } else if ($responseType == self::RESPONSE_URL_ENCODED) {
            return $this->decodeUrlEncodedResponse($responseString);
        }
    }

    /**
     * Checks, if a valid account has been provided
     *
     * @return bool
     * @throws \Exception
     */
    protected function preSendToApiCheck()
    {
        if ($this->getAccount() == null) {
            throw new \Exception("Please provided an account");
        }

        if (!$this->getAccount()->isValid()) {
            throw new \Exception("Please provided valid account");
        }

        return true;
    }

    /**
     * Decodes a responseString, separated by new lines
     *
     * @param $responseString
     * @return array
     */
    protected function decodeNewLineEncodedResponse($responseString)
    {
        // Spliting reponse body
        $parts = explode("\n", $responseString);

        // Getting the status
        $status = trim($parts[0]);

        $ret = array();

        // Valid answer?
        if (is_numeric($status)) {
            // Successful?
            if ($status != "0") {
                $ret["errorCode"]    = $status;
                $ret["errorMessage"] = trim($parts[1]);
                $ret["errorItem"]    = "";
            } else {
                for ($i = 1; $i < count($parts); $i++) {
                    $tmp = preg_split("/[\s\t]+/", $parts[$i], 2);

                    $key   = trim($tmp[0]);
                    $value = trim($tmp[1]);

                    // if key already exists, open new array dimension
                    if (isset($ret[$key])) {
                        if (!is_array($ret[$key])) {
                            $tmp_value   = $ret[$key];
                            $ret[$key]   = array();
                            $ret[$key][] = $tmp_value;
                        } else {
                            $ret[$key][] = $value;
                        }
                    } else {
                        // Just save
                        $ret[$key] = $value;
                    }
                }
            }
        } else {
            $ret["errorCode"]    = "";
            $ret["errorMessage"] = trim($responseString);
            $ret["errorItem"]    = "";
        }

        return $ret;
    }

    /**
     * Decodes a responseString, encoded in query-string-format
     *
     * @param $responseString
     * @return array
     */
    protected function decodeUrlEncodedResponse($responseString)
    {
        // Splitting response body
        $responseString = urldecode($responseString);
        parse_str($responseString, $arr);

        return $arr;
    }
}