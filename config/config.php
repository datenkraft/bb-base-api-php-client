<?php

return array(
    'oAuthTokenUrl' => getenv('X_DATENKRAFT_OAUTH_TOKEN_URL') ?: 'https://authorization-api.conqore.niceshops.com/oauth/token',
    'verifySsl' => !(getenv('X_DATENKRAFT_VERIFY_SSL') === 'false'),
);
