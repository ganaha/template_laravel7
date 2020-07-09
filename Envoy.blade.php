@servers(['web' => 'lightsail'])

@story('deploy')
    git
    composer
    cache
    migrate
@endstory

@task('git')
    cd /opt/bitnami/apache2/htdocs/laravel
    git pull origin master
@endtask

@task('composer')
    cd /opt/bitnami/apache2/htdocs/laravel
    composer install --optimize-autoloader --no-dev
@endtask

@task('cache')
    cd /opt/bitnami/apache2/htdocs/laravel
    php artisan config:cache
    {{-- php artisan route:cache --}}
    php artisan view:cache
@endtask

@task('migrate')
    cd /opt/bitnami/apache2/htdocs/laravel
    php artisan migrate
@endtask
