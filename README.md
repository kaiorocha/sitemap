Crawler sitemap PHP

Esse crawler foi criado para facilitar a captura de links para sites externos.


Exemplo de utilização:

A url do site que deseja extrair os links
$url = 'http://www.dafiti.com.br/';

Instanciação da classe
$sitemap = new Sitemap();

Setar a url para a classe instanciada
$sitemap->set_url($url);

Solicitar a geração do xml com os links extraídos
$sitemap->xml();

Modelo de resposta:

<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0">
  <url>
    <loc>http://www.dafiti.com.br/faq/</loc>
  </url>
  <url>
    <loc>http://www.dafiti.com.br/catalog/?q=vestido</loc>
  </url>
</urlset>
