<?php

namespace PHPSTORM_META {

    override(
        \Datenkraft\Backbone\Client\BaseApi\ClientFactory::createClient(''),
        map(
            [
                '\Datenkraft\Backbone\Client\SkuUsageApi\Generated\Client' => \Datenkraft\Backbone\Client\SkuUsageApi\Generated\Client::class
            ]
        ),
);
}
