<?php
namespace Bolius\BoliusStaticdomain\Hooks;


/**
 * Class Tsfe
 */
class Tsfe
{

    public function contentPostProc_all(&$params, $tsfe )
    {
        print_r($params); die;
    }

    public function contentPostProc_output(&$params, $tsfe )
    {
        print_r($params); die;
    }

}