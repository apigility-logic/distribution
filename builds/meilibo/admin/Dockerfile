FROM webdevops/php-nginx:centos-7-php56

RUN /usr/local/bin/yum-install php56w-pecl-memcache \
    && echo 'date.timezone = Asia/Shanghai' >> /opt/docker/etc/php/php.ini;

COPY nginx.conf /opt/docker/etc/nginx/meilibo/server.conf
COPY fastcgi.conf /opt/docker/etc/nginx/meilibo/fastcgi.conf

RUN sed -i 's#/etc/nginx/conf.d/\*.conf;#/opt/docker/etc/nginx/meilibo/server.conf;#' /opt/docker/etc/nginx/nginx.conf;
RUN ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime;