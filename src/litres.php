<?php /** @noinspection CurlSslServerSpoofingInspection */

namespace xnf4o\litres;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Midnite81\Xml2Array\Xml2Array;

class litres
{
    public $api_url;

    public $xml;

    protected $client;

    private $request_url;

    private $file_xml;

    public function __construct()
    {
        $this->api_url = 'https://'.config('litres.domain').'.litres.ru/';
        $this->client = new Client();
        $this->getUpdate();
    }

    /**
     *
     */
    public function getUpdate(): void
    {
        $this->xml = $this->curlBase($this->getRequestUrl());
        $this->file_xml = time().'.xml';
        Storage::put('public'.DIRECTORY_SEPARATOR.$this->file_xml, $this->xml);
    }

    /**
     * @param $url
     * @param null $referer
     * @param null $data
     * @param null $proxy
     * @param null $options
     * @return bool|string
     */
    private function curlBase($url, $referer = null, $data = null, $proxy = null, $options = null)
    {
        $process = curl_init($url);
        if (! is_null($data)) {
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        }
        if (! is_null($options)) {
            curl_setopt_array($process, $options);
        }
        if (! is_null($proxy)) {
            curl_setopt($process, CURLOPT_PROXY, $proxy);
        }
        if (mb_substr_count($url, 'https://', 'utf-8') > 0) {
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($process, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($process, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.132 Safari/537.36');
        if ($referer !== null) {
            curl_setopt($process, CURLOPT_REFERER, $referer);
        }
        curl_setopt($process, CURLOPT_CONNECTTIMEOUT, 60 * 15);
        curl_setopt($process, CURLOPT_TIMEOUT, 60 * 15);
        @curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $resalt = curl_exec($process);
        curl_close($process);

        return $resalt;
    }

    /**
     * @return string
     */
    private function getRequestUrl(): string
    {
        $this->request_url = $this->api_url.'get_fresh_book/'.$this->getCheckpoint().'&place='.config('litres.id').'&type=all&timestamp='.time().'&sha='.$this->getSHA256();

        return $this->request_url;
    }

    /**
     * @return string
     */
    private function getCheckpoint(): string
    {
        return '?checkpoint='.urlencode(Carbon::now()->format('Y-m-d H:i:s'));
    }

    /**
     * @return string
     */
    private function getSHA256(): string
    {
        return hash('sha256', time().':'.config('litres.key').':'.Carbon::now()->format('Y-m-d H:i:s'), false);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFreshBook(): JsonResponse
    {
        return $this->parseXML();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function parseXml(): JsonResponse
    {
        $source = file_get_contents('storage/'.$this->file_xml);

        return response()->json(Xml2Array::create($source)->toCollection());
    }
}
