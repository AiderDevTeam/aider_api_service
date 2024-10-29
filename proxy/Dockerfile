FROM httpd:2.4

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    && rm -r /var/lib/apt/lists/*

#RUN apt-get install vim

RUN echo "LoadModule proxy_module modules/mod_proxy.so" >> /usr/local/apache2/conf/httpd.conf \
    && echo "LoadModule proxy_http_module modules/mod_proxy_http.so" >> /usr/local/apache2/conf/httpd.conf \
    && echo "LoadModule unixd_module modules/mod_unixd.so" >> /usr/local/apache2/conf/httpd.conf\
    && echo "LoadModule ssl_module modules/mod_ssl.so" >> /usr/local/apache2/conf/httpd.conf \
    && echo "LoadModule ssl_module modules/mod_ssl.so" >> /usr/local/apache2/conf/httpd.conf \
    && echo "LoadModule access_compat_module modules/mod_access_compat.so" >> /usr/local/apache2/conf/httpd.conf 

#COPY httpd.conf /usr/local/apache2/conf/httpd.conf

RUN rm -rf /usr/local/apache2/conf/httpd.conf

COPY httpd.conf /usr/local/apache2/conf/httpd.conf

# RUN rm -rf /usr/local/apache2/apache_ssl.sh
# COPY apache_ssl.sh /usr/local/apache2/apache_ssl.sh
# RUN chmod +x /usr/local/apache2/apache_ssl.sh
# RUN /usr/local/apache2/apache_ssl.sh

EXPOSE 80
EXPOSE 443