<?php
namespace Checkdomain\Comodo;

use Checkdomain\Comodo\Model\Account;
use Guzzle\Http\Client;

/**
 * Class CommunicationAdapter
 *
 * Manages the communication with comodo
 */
class CommunicationAdapter
{
    const RESPONSE_NEW_LINE    = 0;
    const RESPONSE_URL_ENCODED = 1;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Account
     */
    protected $account;

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

    /*
    **
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
     * Sends a query to the provided url and return the response body.
     *
     * @param $url
     * @param $params
     *
     * @return string
     */
    public function sendToWebsite($url, $params)
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
    public function sendToApi($url, $params, $responseType = self::RESPONSE_NEW_LINE)
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
     * Decodes a responseString, separated by new lines and returns an response array
     *
     * @param $responseString
     *
     * @return array
     */
    protected function decodeNewLineEncodedResponse($responseString)
    {
        // Splitting response body
        $parts = explode("\n", $responseString);

        // Getting the status
        $status = trim($parts[0]);

        $responseArray = array();

        // Valid answer?
        if (is_numeric($status)) {
            // Successful?
            if ($status != "0") {
                $responseArray["errorCode"]    = $status;
                $responseArray["errorMessage"] = trim($parts[1]);
            } else {
                for ($i = 1; $i < count($parts); $i++) {
                    $tmp = preg_split("/[\s\t]+/", $parts[$i], 2);

                    $key   = trim($tmp[0]);
                    $value = trim($tmp[1]);

                    // if key already exists, open new array dimension
                    if (isset($responseArray[$key])) {
                        if (!is_array($responseArray[$key])) {
                            $tmp_value             = $responseArray[$key];
                            $responseArray[$key]   = array();
                            $responseArray[$key][] = $tmp_value;
                        } else {
                            $responseArray[$key][] = $value;
                        }
                    } else {
                        // Just save
                        $responseArray[$key] = $value;
                    }
                }
            }
        } else {
            $responseArray["errorCode"]    = "";
            $responseArray["errorMessage"] = trim($responseString);
        }

        $responseArray["responseString"] = $responseString;

        return $responseArray;
    }

    /**
     * Decodes a responseString, encoded in query-string-format and returns an response array
     *
     * @param $responseString
     * @return array
     */
    protected function decodeUrlEncodedResponse($responseString)
    {
        // Splitting response body
        $responseString = urldecode($responseString);
        parse_str($responseString, $responseArray);

        $responseArray["responseString"] = $responseString;

        return $responseArray;
    }
}