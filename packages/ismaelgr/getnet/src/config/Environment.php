<?php

namespace ismaelgr\getnet\config;

/**
 * Class Environment.
 */
class Environment
{
   
    private function local()
    {
       
        $api = 'https://api-sandbox.getnet.com.br/';

        return $api;
    }

    private function sandbox()
    {
        
        $api = 'https://api-sandbox.getnet.com.br/';

        return $api;
    }

    private function homologation()
    { 
        $api = 'https://api-homologacao.getnet.com.br/';
        
    
        return $api;
    }

    
    private function production()
    {
        $api = 'https://api.getnet.com.br/';

        return $api;
    }

    public function getApiUrl()
    {
        $config = config('app.env');
        $environment = $this->$config();
        // dd( $environment);
        return $environment;
    }
}
