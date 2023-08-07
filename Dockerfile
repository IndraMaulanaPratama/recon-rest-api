FROM 10.168.26.20/library/php-laravel-octane-alpine:latest
WORKDIR /usr/recon-api
COPY . .
RUN ls -la && composer install
EXPOSE 8000
CMD ["./entrypoint.sh"]