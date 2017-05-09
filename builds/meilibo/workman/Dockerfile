FROM webdevops/php:centos-7-php56

RUN /usr/local/bin/yum-install php56w-pecl-memcache \
    && echo 'date.timezone = Asia/Shanghai' >> /opt/docker/etc/php/php.ini;

EXPOSE 4646
EXPOSE 4545
EXPOSE 7272
EXPOSE 1236

COPY workman_start.sh /entrypoint.d/workman_start.sh