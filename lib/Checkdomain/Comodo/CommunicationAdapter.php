<?php
namespace Checkdomain\Comodo;

use Checkdomain\Comodo\Model\Account;
use GuzzleHttp\Client;

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
     * @return CommunicationAdapter
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
     * Constructs a communication adapter with an account
     *
     * @param Account $account
     */
    public function __construct(Account $account = null)
    {
        $this->account = $account;
    }

    /**
     * @param \GuzzleHttp\Client $client
     *
     * @return CommunicationAdapter
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Sends a query to the provided url and return the response body.
     *
     * @param string $url
     * @param array  $params
     *
     * @return string
     */
    public function sendToWebsite($url, array $params)
    {
        // Sending request
        $client  = $this->getClient();
        $response = $client->request('POST', $url, [
            'query' => http_build_query($params, '', '&')
        ]);

        // Getting response body
        $responseString = $response->getBody()->getContents();
        $responseString = trim($responseString);

        return $responseString;
    }

    /**
     * Send a request to the comodo API, and decodes the response as given
     *
     * @param string     $url
     * @param array      $params
     * @param int        $responseType
     * @param array|null $notDecode
     * @param array      $forceArray
     *
     * @return array|bool
     */
    public function sendToApi($url, array $params, $responseType = self::RESPONSE_NEW_LINE, array $notDecode = null, $forceArray = array())
    {
        $this->preSendToApiCheck();

        // Merging post-data
        $fields                  = array();
        $fields["loginName"]     = $this->getAccount()->getLoginName();
        $fields["loginPassword"] = $this->getAccount()->getLoginPassword();
        $fields                  = array_merge($fields, $params);

        // Sending request
        $client   = $this->getClient();
        $response = $client->request('POST', $url, [
            'form_params' => $fields
        ]);
        $query    = http_build_query($params);

        // Getting response body
        $responseString = $response->getBody()->getContents();
        $responseString = trim($responseString);

        // Decoding and returning response
        if ($responseType == self::RESPONSE_NEW_LINE) {
            return $this->decodeNewLineEncodedResponse($responseString, $query, $forceArray);
        } else {
            return $this->decodeUrlEncodedResponse($responseString, $query, $notDecode, $forceArray);
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
        if ($this->getAccount() === null) {
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
     * @param string $responseString
     * @param string $requestQuery
     * @param array  $forceArray
     *
     * @return array
     */
    protected function decodeNewLineEncodedResponse($responseString, $requestQuery, array $forceArray = array())
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
                $responseArray["errorCode"]    = $status;

                $partCount = count($parts);
                for ($i = 1; $i < $partCount; $i++) {
                    $tmp = preg_split('/[\s\t]+/', $parts[$i], 2);

                    $key   = trim($tmp[0]);
                    $value = trim($tmp[1]);

                    // if key already exists, open new array dimension
                    if (isset($responseArray[$key])) {
                        if (!is_array($responseArray[$key])) {
                            $tmpValue              = $responseArray[$key];
                            $responseArray[$key]   = array();
                            $responseArray[$key][] = $tmpValue;
                            $responseArray[$key][] = $value;
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

        foreach ($forceArray as $value) {
            if (isset($responseArray[$value]) && !is_array($responseArray[$value])) {
                $responseArray[$value] = array($responseArray[$value]);
            }
        }

        $responseArray["responseString"] = $responseString;
        $responseArray["requestQuery"]   = $requestQuery;

        return $responseArray;
    }

    /**
     *  Decodes a responseString, encoded in query-string-format and returns an response array
     *
     * @param string   $responseString
     * @param string   $requestQuery
     * @param array    $notDecode
     * @param string[] $forceArray
     *
     * @return mixed
     */
    protected function decodeUrlEncodedResponse(
        $responseString,
        $requestQuery,
        array $notDecode = null,
        $forceArray = array()
    )
    {
        if (empty($notDecode)) {
            $responseString = urldecode($responseString);
        }

        if (!empty($forceArray)) {
            foreach ($forceArray as $param) {
                $responseString = str_replace($param . '=', $param .'[]=', $responseString);
            }
        }

        // Splitting response body
        $responseArray = [];
        parse_str($responseString, $responseArray);

        if (!empty($notDecode) && is_array($responseArray)) {
            foreach ($responseArray as $index => $value) {
                if (!in_array($index, $notDecode)) {
                    $value = urldecode($value);
                }

                $responseArray[$index] = $value;
            }
        }

        $responseArray["responseString"] = $responseString;
        $responseArray["requestQuery"]   = $requestQuery;

        if (!isset($responseArray['errorCode'])) {
            $responseArray['errorCode'] = 99;
            $responseArray['errorMessage'] = $responseString;
        }

        return $responseArray;
    }
}
