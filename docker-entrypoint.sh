#!/bin/sh
set -e

PATH_FIC_PARAM="/config/environment.properties"
export BASE_URL_SSO=$(grep "keycloak.baseUrl=" $PATH_FIC_PARAM 2>/dev/null | sed 's/^[^=]*=//')
#récupérer secret sso aussi pour mettre dans la config

apache2-foreground
