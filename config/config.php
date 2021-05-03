<?php

return array(
    'oAuthTokenUrl' => getenv('X_DATENKRAFT_OAUTH_TOKEN_URL') ?: 'UNDEFINED',
    'verifySSL' => getenv('X_DATENKRAFT_VERIFY_SSL') ?: 'true',
);
