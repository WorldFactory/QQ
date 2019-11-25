<?php

namespace WorldFactory\QQ\Services\Runners;

use Exception;
use Symfony\Component\Process\Process;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\Buffer;

class CurlRunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'headers' => [
            'type' => 'array',
            'description' => "Array of headers.",
            'default' => []
        ],
        'method' => [
            'type' => 'string',
            'description' => "HTTP method. Default is GET.",
            'default' => 'GET'
        ],
        'timeout' => [
            'type' => 'int',
            'description' => "Request timeout.",
            'default' => 30
        ],
        'data' => [
            'type' => 'array',
            'description' => "Fields of the request.",
            'default' => []
        ],
        'protocol' => [
            'type' => 'string',
            'description' => "Internet protocol used for request.",
            'default' => 'http'
        ],
        'persist' => [
            'type' => 'bool',
            'description' => "Use cookie to persists connexion.",
            'default' => true
        ],
        'file' => [
            'type' => 'string',
            'description' => "File to upload. You can use relative path.",
            'default' => ''
        ],
        'debug' => [
            'type' => 'bool',
            'description' => "Increase CURL verbosity.",
            'default' => false
        ],
        'encoding' => [
            'type' => 'string',
            'description' => "Data encoding. Accept 'json', 'url' or 'none'.",
            'default' => 'none'
        ]
    ];

    protected const SHORT_DESCRIPTION = "Execute Curl request and get result.";

    protected const LONG_DESCRIPTION = <<<EOT
Execute Curl request and get result.
Throw error if HTTP response code is not 200.
EOT;

    private const HTTP_CODES = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // WebDAV; RFC 2518
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information', // since HTTP/1.1
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // WebDAV; RFC 4918
        208 => 'Already Reported', // WebDAV; RFC 5842
        226 => 'IM Used', // RFC 3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other', // since HTTP/1.1
        304 => 'Not Modified',
        305 => 'Use Proxy', // since HTTP/1.1
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect', // since HTTP/1.1
        308 => 'Permanent Redirect', // approved as experimental RFC
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC 2324
        419 => 'Authentication Timeout', // not in RFC 2616
        420 => 'Enhance Your Calm (Twitter) / Method Failure (Spring Framework)', // Twitter / Spring Framework
        422 => 'Unprocessable Entity', // WebDAV; RFC 4918
        423 => 'Locked', // WebDAV; RFC 4918
        424 => 'Failed Dependency (WebDAV; RFC 4918) / Method Failure (WebDAV)', // WebDAV; RFC 4918 / WebDAV
        425 => 'Unordered Collection', // Internet draft
        426 => 'Upgrade Required', // RFC 2817
        428 => 'Precondition Required', // RFC 6585
        429 => 'Too Many Requests', // RFC 6585
        431 => 'Request Header Fields Too Large', // RFC 6585
        444 => 'No Response', // Nginx
        449 => 'Retry With', // Microsoft
        450 => 'Blocked by Windows Parental Controls', // Microsoft
        451 => 'Redirect (Microsoft) / Unavailable For Legal Reasons (Internet draft)', // Microsoft / Internet draft
        494 => 'Request Header Too Large', // Nginx
        495 => 'Cert Error', // Nginx
        496 => 'No Cert', // Nginx
        497 => 'HTTP to HTTPS', // Nginx
        499 => 'Client Closed Request', // Nginx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // RFC 2295
        507 => 'Insufficient Storage', // WebDAV; RFC 4918
        508 => 'Loop Detected', // WebDAV; RFC 5842
        509 => 'Bandwidth Limit Exceeded', // Apache bw/limited extension
        510 => 'Not Extended', // RFC 2774
        511 => 'Network Authentication Required', // RFC 6585
        598 => 'Network read timeout error', // Unknown
        599 => 'Network connect timeout error', // Unknown
    );

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script)
    {
        $options = $this->getOptions();

        $request = curl_init();

        $method = $options['method'];

        $url = "{$options['protocol']}://$script";

        curl_setopt($request, CURLOPT_URL, $url);

        curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($request, CURLOPT_MAXREDIRS, 5);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

        if ($options['persist']) {
            $cookieFileName = $this->getCookieFilename($url);

            echo "Cookie file : $cookieFileName" . PHP_EOL;

            curl_setopt($request, CURLOPT_COOKIEJAR, $cookieFileName);
            curl_setopt($request, CURLOPT_COOKIEFILE, $cookieFileName);
        }

        $data = $options['data'];

        if ($options['file']) {
            $path = realpath($options['file']);
            if (!file_exists($path)) {
                throw new Exception("File not found : {$options['file']}");
            }

            $data['file'] = "@$path";
        }

        if (!empty($data)) {
            switch ($options['encoding']) {
                case 'json':
                    $data = json_encode($data);
                    break;
                case 'url':
                    $data = urlencode($data);
                    break;
                case 'none':
                    break;
                default:
                    throw new Exception("Unknown data encoding : '{$options['encoding']}'.");
                    break;
            }

            curl_setopt($request, CURLOPT_POSTFIELDS, $data);

            if ($method === 'GET') {
                $method = 'POST';
            }
        }

        curl_setopt($request, CURLOPT_VERBOSE, $options['debug']);
        curl_setopt($request, CURLOPT_TIMEOUT, $options['timeout']);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $this->aggregateHeaders($options['headers']));

        $output = curl_exec($request);

        $code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        curl_close($request);

        if ($code !== 200) {
            throw new Exception("HTTP code #{$code} : " . self::HTTP_CODES[$code] );
        }

        $length = strlen($output);
        echo "Response length : $length octets." . PHP_EOL;

        return $output;
    }

    protected function getCookieFilename(string $url) : string
    {
        $varDir = getcwd() . '/var/tmp/cookie';

        if (!is_dir($varDir)) {
            mkdir($varDir, 0777, true);
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (empty($host)) {
            throw new Exception("Unable to retrieve host in target url : $url");
        }

        return "$varDir/$host";
    }

    protected function aggregateHeaders(array $headers)
    {
        $aggregatedHeaders = [];

        foreach($headers as $key => $val) {
            $aggregatedHeaders[] = "$key: $val";
        }

        return $aggregatedHeaders;
    }
}