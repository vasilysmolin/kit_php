#!/usr/bin/env sh
set -e

DEV=/etc/nginx/conf.d/development.conf;
LOCAL=/etc/nginx/conf.d/local.conf;
PROD=/etc/nginx/conf.d/production.conf;

PHPMYADMIN=/etc/nginx/conf.d/phpmyadmin.conf
sed -i "s#%DOMAIN%#${DOMAIN}#g" "$PHPMYADMIN";

MARKET=/etc/nginx/conf.d/market.conf
sed -i "s#%DOMAIN%#${DOMAIN}#g" "$MARKET";
sed -i "s#%LOCAL_DIR%#${LOCAL_DIR}#g" "$MARKET";

CRM=/etc/nginx/conf.d/crm.conf
sed -i "s#%DOMAIN%#${DOMAIN}#g" "$CRM";
sed -i "s#%LOCAL_DIR%#${LOCAL_DIR}#g" "$CRM";

if test -f "$DEV"; then
    sed -i "s#%DOMAIN%#${DOMAIN}#g" "$DEV";
    sed -i "s#%LOCAL_DIR%#${LOCAL_DIR}#g" "$DEV";
    sed -i "s#%BACK_DIR%#${BACK_DIR}#g" "$DEV";
    sed -i "s#%AUTH_DIR%#${AUTH_DIR}#g" "$DEV";
    sed -i "s#%LINK_DIR%#${LINK_DIR}#g" "$DEV";
    sed -i "s#%CRM_DIR%#${CRM_DIR}#g" "$DEV";
fi
if test -f "$LOCAL"; then
    sed -i "s#%DOMAIN%#${DOMAIN}#g" "$LOCAL";
    sed -i "s#%BACK_DIR%#${BACK_DIR}#g" "$LOCAL";
    sed -i "s#%LOCAL_DIR%#${LOCAL_DIR}#g" "$LOCAL";
    sed -i "s#%AUTH_DIR%#${AUTH_DIR}#g" "$LOCAL";
    sed -i "s#%LINK_DIR%#${LINK_DIR}#g" "$LOCAL";
    sed -i "s#%CRM_DIR%#${CRM_DIR}#g" "$LOCAL";
fi
if test -f "$PROD"; then
    sed -i "s#%DOMAIN%#${DOMAIN}#g" "$PROD";
    sed -i "s#%BACK_DIR%#${BACK_DIR}#g" "$PROD";
    sed -i "s#%LOCAL_DIR%#${LOCAL_DIR}#g" "$PROD";
    sed -i "s#%AUTH_DIR%#${AUTH_DIR}#g" "$PROD";
    sed -i "s#%LINK_DIR%#${LINK_DIR}#g" "$PROD";
    sed -i "s#%CRM_DIR%#${CRM_DIR}#g" "$PROD";
fi

exec "$@";
