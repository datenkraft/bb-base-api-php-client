<?php

return array(
    'oAuthTokenUrl' => getenv('X_DATENKRAFT_OAUTH_TOKEN_URL') ?: 'https://authorization-api.bb-3037.gcp.datenkraft.info/oauth/token',
    'verifySsl' => getenv('X_DATENKRAFT_VERIFY_SSL') ?: 'true',
);
