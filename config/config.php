<?php

return array(
    'oAuthScopes' => [],
    'oAuthTokenUrl' => getenv('X_DATENKRAFT_OAUTH_TOKEN_URL') ?:
        'https://authentication-api.conqore.niceshops.com/oauth/token',
    'verifySsl' => !(getenv('X_DATENKRAFT_VERIFY_SSL') === 'false'),
);
