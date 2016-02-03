<?php
/**
 * Created by PhpStorm.
 * User: esilva
 * Date: 02/02/2016
 * Time: 14:08
 */

namespace UOL\PagSeguro\Helper;


class PagSeguroLibrary
{

    const LIBRARY_AUTOLOAD = BP.'/app/code/UOL/PagSeguro/vendor/autoload.php';

    public function __construct()
    {
        require_once(self::LIBRARY_AUTOLOAD);
        \PagSeguroLibrary::init();
    }

    public function getEnvironment()
    {
        return \PagSeguroConfig::getEnvironment();
    }

    public function PagSeguroShipping()
    {
        return new \PagSeguroShipping();
    }



}