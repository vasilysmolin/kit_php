FROM nginx:stable-alpine

ARG DOMAIN
ARG ENV
ARG BACK_DIR
ARG LOCAL_DIR
#ENV PATH="${PATH}:/sbin"
#CMD "/bin/sh"
#RUN apk add --no-cache gcc musl-dev python3 libxml2 libxml2-dev libxslt libxslt-dev
#RUN pip3 install --upgrade pip
#RUN pip3 install --upgrade pelican Markdown typogrify BeautifulSoup4 lxml


COPY /${ENV}.conf /etc/nginx/conf.d/
#COPY /phpmyadmin.conf /etc/nginx/conf.d/

ENV DOMAIN=${DOMAIN}
ENV BACK_DIR=${BACK_DIR}
ENV LOCAL_DIR=${LOCAL_DIR}

RUN apk update \
    && apk add openssl \
    && mkdir /etc/nginx/ssl \
    && openssl req -x509 -nodes -days 365 \
        -subj '/C=CA/ST=QC/O=Company, Inc./CN=${DOMAIN}' \
        -addext 'subjectAltName=DNS:'${DOMAIN} \
        -newkey rsa:4096 \
        -keyout /etc/nginx/ssl/${DOMAIN}.key -out /etc/nginx/ssl/${DOMAIN}.cer

RUN mkdir -p /var/lib/nginx/cache \
    && chmod 700 /var/lib/nginx/cache

COPY /nginx.conf /etc/nginx/
COPY /ssl/${ENV} /etc/nginx/ssl/
COPY /nginx-entrypoint.sh /nginx-entrypoint.sh

RUN chmod +x /nginx-entrypoint.sh

ENTRYPOINT ["sh", "/nginx-entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]

