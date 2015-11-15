<?php

class Curl {
    
    public $curl;
    public $manual_follow;
    public $redirect_url;
    public $headers = array();

    function Curl() {
        $this->curl = curl_init();
        $this->headers[] = "Accept: */*";
        $this->headers[] = "Cache-Control: max-age=0";
        $this->headers[] = "Connection: keep-alive";
        $this->headers[] = "Keep-Alive: 300";
        $this->headers[] = "Accept-Charset: utf-8;ISO-8859-1;iso-8859-2;q=0.7,*;q=0.7";
        $this->headers[] = "Accept-Language: en-us,en;q=0.5";
        $this->headers[] = "Pragma: "; // browsers keep this blank.

        
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.0.14) Gecko/2009082707 Firefox/3.0.14 (.NET CLR 3.5.30729)');
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);

        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')){
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        } else {
            $this->manual_follow = true;
        }

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 0);

        $this->setRedirect();
    }
    
    function addHeader($header){
        $this->headers[] = $header;
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);        
    }
    
    function header($val){
        curl_setopt($this->curl, CURLOPT_HEADER, $val);
    }
    
    function close() {
          curl_close($this->curl);
    }
    
    function getInfo(){
          return curl_getinfo($this->curl);
    }

    function setTimeout($connect, $transfer) {
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $connect);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $transfer);
    }

    function getError() {
        return curl_errno($this->curl) ? curl_error($this->curl) : false;
    }

    function setRedirect($enable = true) {
        if ($enable) {
            $this->manual_follow = !curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        } else {
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
            $this->manual_follow = false;
        }
    }

    function getHttpCode() {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }


    function makeQuery($data) { 
        if (is_array($data)) {
            $fields = array();
            foreach ($data as $key => $value) {
                 $fields[] = $key . '=' . urlencode($value);
            }
            $fields = implode('&', $fields);
        } else {
            $fields = $data;
        }

        return $fields;
    }
    
    function get($url, $data = null) {
        
        curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, true);
        if (!is_null($data)) {
            $fields = $this->makeQuery($data);
            $url .= '?' . $fields;
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);
        $page = curl_exec($this->curl);

        $error = curl_errno($this->curl);

        if ($error != CURLE_OK || empty($page)) {
            return false;
        }
        
        return $page;
    }
}

?>
