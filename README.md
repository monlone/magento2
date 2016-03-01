Módulo de integração PagSeguro para Magento
===========================================

[![Code Climate](https://codeclimate.com/github/esilvajr/magento2/badges/gpa.svg)](https://codeclimate.com/github/esilvajr/magento2)

Importante
----------
Essa versão não pode ser utilizada em produção.

Instalação
----------
1. Copie os arquivos baixados para o seu diretório do magento: <your Magento install dir>/app/code/

2. Procure por <your Magento install dir>/app/etc/config.php e abra em um editor de texto ou ide.
  Adicione o comando após ‘modules’ => array (
  'UOL_PagSeguro' => 1  

3. No diretório <your Magento install dir>/app/code/UOL/PagSeguro, execute os seguinte comando.
  Composer Update.

4. No diretório raiz do magento <your Magento install dir> execute:
   php bin/magento setup:upgrade
   chmod 777 -R var/ pub/

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
