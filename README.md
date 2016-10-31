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
