<?php

namespace App\Providers;

use App\Models\Store;
use App\Models\StoreConfig;
use Illuminate\Support\ServiceProvider;

class StoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // $this->changeDatabaseName();
        // $this->getStoreConfig();
    }

    /**
     * Register services.
     */
    public function register()
    {
    }

    /**
     * Change the default database name so each shop use it's own data.
     */
    public function changeDatabaseName()
    {
        // Dominios que usarão o ENV padrão (database local)

        $_SERVER['HTTP_HOST'] = 'padrao.dashboard';
        $defaultEnvCases = explode(',', env('DEVELOPMENT_DATABASE_URL', 'localhost'));
        $domainExists = Store::where('domain', $_SERVER['HTTP_HOST'])->count();

        // Se o dominio não existir, retorna 404.
        // Comando para criação de dominio: php artisan domain:create
        if (!$domainExists) {
            return abort(404);
        }

        /*
         * Ja que rodamos a aplicaçao atraves de um artisan serve, o PHP
         * nao reconhece o comando HTTP_HOST, por isso fazemos um isset
         * e depois alteramos o database de acordo com o dominio acessado.
         */
        if (isset($_SERVER['HTTP_HOST']) && env('APP_ENV') != 'local') {
            if (!in_array($_SERVER['HTTP_HOST'], $defaultEnvCases)) {
                config([
                    // Trocamos apenas o database, as credenciais permanecem
                    // 'database.mysql.database' => str_replace( str_split('-. @'), '', $_SERVER['HTTP_HOST'] )
                    'database.dashboard_server.database' => str_replace(str_split('-. @'), '', $_SERVER['HTTP_HOST']),
                ]);
            }
        }
    }

    public function getStoreConfig()
    {
        $storeID = Store::where('domain', $_SERVER['HTTP_HOST'])->pluck('id');
        $storeConfig = StoreConfig::where('store_id', $storeID);

        return $storeConfig;
    }
}
