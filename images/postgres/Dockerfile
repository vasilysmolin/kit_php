FROM postgres:13-alpine

COPY /postgresql.conf      /tmp/postgresql.conf
COPY /updateConfig.sh      /docker-entrypoint-initdb.d/_updateConfig.sh
