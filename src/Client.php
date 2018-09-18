<?php

/*
 * This file is part of the Psolr package.
 *
 * (c) wuzx <404220273@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psolr;


class Client implements ClientInterface
{
    const VERSION = '1.0.0';

    protected $solrUrl;
    protected $pageSize = 20;

    /**
     * @param array $params    host,port,solrname for solr.
     */
    public function __construct($params = null)
    {
        $this->solrUrl = $params['host'] . ': ' . $params['port'] . '/solr/' . $params['name'];
    }

    public function select($q, $fl = null, $op = null, $page = null, $pageSize = null)
    {
        $urlParams = 'q=' . urlencode($q);
        if ($op) {
            $urlParams .= "&q.op=" . $op;
        }
        if ($fl) {
            $urlParams .= "&fl=" . $fl;
        }
        $pageSize = $pageSize ?? $this->pageSize;
        $urlParams .= "&rows=" . $pageSize;
        if ($page) {
            $start = ($page - 1) * $pageSize;
            $urlParams .= "&start=" . $start;
        }
        $result = $this->requestSolr($urlParams);
        return $result;
    }

    private function requestSolr($urlParams, $type='select') {
        $ch=curl_init();
        $url = $this->solrUrl . "/" . $type . "?" . $urlParams;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_TIMEOUT,10);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $responseStr = curl_exec($ch);
        $responseArr = [];
        if ($responseStr) {
            $responseArr = json_decode($responseStr,true);
        }
        return $responseArr;
    }

}
