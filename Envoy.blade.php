@servers(['web' => 'lightsail'])

@story('deploy')
    git
    composer
    npm
    cache
    migrate
@endstory

@task('git')
    cd /opt/bitnami/apache2/htdocs
    git pull origin master
@endtask

@task('composer')
    composer install --optimize-autoloader --no-dev
@endtask

@task('npm')
    npm install
@endtask

@task('cache')
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
@endtask

@task('migrate')
    php artisan migrate
@endtask
