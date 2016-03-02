Módulo de integração PagSeguro para Magento
===========================================

[![Code Climate](https://codeclimate.com/github/esilvajr/magento2/badges/gpa.svg)](https://codeclimate.com/github/esilvajr/magento2)

Importante
----------
Essa versão não pode ser utilizada em produção.

Instalação
----------

- 1. Instale via packagist 
	- composer require esilvajr/pagseguro-magento2
- 2. Execute o comando: 
	- php bin/magento setup:upgrade
- 3. Dê permissões as pastas var/ pub/
	- chmod 777 -R var/ pub/

Changelog
---------
1.0.0
- Nova release.

0.0.2-alpha
- Integração com API de notificação.
- Configuração do Setup do módulo.

0.0.1-alpha
- Adicionado meio de pagamento ao Magento2
- Integração com API de Pagamento do PagSeguro.
- Versão inicial.
