<?php

return array(
    'oAuthTokenUrl' => getenv('X_DATENKRAFT_OAUTH_TOKEN_URL') ?: 'UNDEFINED',
    'verifySsl' => getenv('X_DATENKRAFT_VERIFY_SSL') ?: 'true',
);
