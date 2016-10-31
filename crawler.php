<?php

/***
 * Class Sitemap
 * Version 0.7.3
 * Author Kaio Rocha
 */

class Sitemap {

    const HTTP_CODE_200 = 200;
    const HTTP_CODE_301 = 301;

    protected $arquivo;
    protected $url;

    protected $ignorar = array();

    protected $extensao;

    protected $xmlns = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    protected $xmlns_xsi = 'http://www.w3.org/2001/XMLSchema-instance';
    protected $xmlns_schemaLocation = 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';
    protected $xmlns_mobile = 'http://www.google.com/schemas/sitemap-mobile/1.0';

    protected $scaneados = array();

    private function pasta($pasta){
        $arquivo = explode ("/", $pasta);
        $tamanho = strlen ($arquivo[count ($arquivo) - 1]);
        return (substr ($pasta, 0, strlen ($pasta) - $tamanho));
    }

    public function set_url($url){
        $this->url = $url;
    }

    private function get_url(){
        return $this->url;
    }

    private function acessar_url(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->get_url());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $dados = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode == self::HTTP_CODE_200){
            return $dados;
        }

        $this->set_url(str_replace('http:', 'https:', $this->get_url()));

        return $this->acessar_url();
    }

    public function scanear($sub = true){
        $scaneados = array();

        array_push ($scaneados, $this->url);
        $html = $this->acessar_url();

        $links = explode ("<a", $html);

        array_unique($links);

        foreach($links as $key => $link){

            $a_partes = explode("href=", $link);

            if(count($a_partes) > 1){
                $href_partes = explode (" ", $a_partes[1]);
                $href_partes2 = explode ("#", $href_partes[0]);

                $href = str_replace ("\"", "", $href_partes2[0]);

                if ((substr ($href, 0, 7) != "http://") && (substr ($href, 0, 8) != "https://") && (substr ($href, 0, 6) != "ftp://")){
                    if (isset($href[0]) && $href[0] != '/'){
                        $href = $this->pasta($this->url);
                    }
                }

                if (substr($href, 0, strlen ($scaneados[0])) == $scaneados[0]){
                    $href = explode('>', $href);
                    $this->scaneados[] = $href[0];
                    if($sub == true && !in_array($href[0], $this->scaneados)){
                        $this->set_url($href[0]);
                        $this->scanear(true);
                    }
                }
            }
        }

        $this->scaneados = array_unique($this->scaneados);

        return $this->scaneados;
    }

    public function xml(){
        $this->scanear();

        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $root = $dom->createElement("urlset");
        $root->setAttribute('xmlns', $this->xmlns);
        $root->setAttribute('xmlns:xsi', $this->xmlns_xsi);
        $root->setAttribute('xsi:schemaLocation', $this->xmlns_schemaLocation);
        $root->setAttribute('xmlns:mobile', $this->xmlns_mobile);

        $i = 0;
        $url = [];

        foreach ($this->scaneados as $link) {
            $url[$i] = $dom->createElement('url');
            $loc = $dom->createElement("loc", $link);
            $url[$i]->appendChild($loc);
            $i++;
        }

        for ($i = 0; $i < count($url); $i++) {
            $root->appendChild($url[$i]);
        }

        $dom->appendChild($root);

        header("Content-Type: text/xml");
        print $dom->saveXML();
    }
}

set_time_limit(0);

$filename = 'sitemap.xml';
$url = 'http://www.dafiti.com.br/';

$ignore = array();

$sitemap = new Sitemap();
$sitemap->set_url($url);
$sitemap->xml();