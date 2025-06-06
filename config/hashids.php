<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        'main' => [
            'salt' => 'padraomeadmin',
            'length' => '24',
        ],

        'alternative' => [
            'salt' => 'padraomeharder',
            'length' => '48',
        ],

        'Sku' => [
            'salt' => 'padraosku',
            'length' => '16',
          ],

          'sku' => [
            'salt' => 'padraosku',
            'length' => '16',
          ],

        'Product' => [
            'salt' => 'padraoproduct',
            'length' => '8',
          ],

        'Reseller' => [
            'salt' => 'padraoreseller',
            'length' => '16',
          ],
    ],
];
