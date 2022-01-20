<?php

namespace Checkdomain\Comodo;

use GuzzleHttp\MessageFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class SanitizingMessageFormatter
 */
class SanitizingMessageFormatter extends MessageFormatter
{
    /** @var string Template used to format log messages */
    private $template;

    /**
     * @var array
     */
    private $denyList;

    /**
     * @param string $template  Log message template
     * @param array  $denyList
     */
    public function __construct($template, array $denyList = [])
    {
        parent::__construct($template);
        $this->template = $template;
        $this->blacklist = $denyList;
    }

    /**
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     * @param \Exception|null        $error
     *
     * @return string|string[]|null
     */
    public function format(RequestInterface $request, ResponseInterface $response = null, \Exception $error = null)
    {
        $cache = [];

        return preg_replace_callback(
            '/{\s*([A-Za-z_\-\.0-9]+)\s*}/',
            function (array $matches) use ($request, $response, $error, &$cache) {
                if (isset($cache[$matches[1]])) {
                    return $cache[$matches[1]];
                }

                $result = '';
                switch ($matches[1]) {
                    case 'req_body':
                        $result = $this->sanitizeBody($request->getBody());
                        break;
                    case 'res_body':
                        $result = $response ? $this->sanitizeBody($response->getBody()) : 'NULL';
                        break;
                    case 'method':
                        $result = $request->getMethod();
                        break;
                    case 'version':
                        $result = $request->getProtocolVersion();
                        break;
                    case 'uri':
                    case 'url':
                        $result = $request->getUri();
                        break;
                    case 'code':
                        $result = $response ? $response->getStatusCode() : 'NULL';
                        break;
                }

                $cache[$matches[1]] = $result;

                return $result;
            },
            $this->template
        );
    }

    /**
     * @param StreamInterface $body
     *
     * @return string
     */
    private function sanitizeBody(StreamInterface $body)
    {
        $body->rewind();
        $content = $body->getContents();

        if (false === empty($content)) {
            $parsed = [];
            parse_str($content, $parsed);

            if (is_array($parsed)) {
                $parsed = $this->sanitize($parsed);
            }
        }

        return false === empty($parsed) ? http_build_query($parsed) : '';
    }

    /**
     * @param array $iterable
     *
     * @return array
     */
    private function sanitize(array $iterable)
    {
        $filtered = [];

        foreach ($iterable as $key => $item) {
            if (is_string($key) && is_string($item)) {
                if (in_array($key, $this->blacklist)) {
                    $item = 'sanitized';
                }
            }

            if (is_array($item)) {
                $item = $this->sanitize($item);
            }

            $filtered[$key] = $item;
        }

        return $filtered;
    }
}
