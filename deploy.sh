composer install --ignore-platform-reqs
php bin/hyperf.php migrate
php bin/hyperf.php seed:run --class UserSeed
php bin/hyperf.php seed:run --class PermissionSeed
php bin/hyperf.php seed:run --class RoleSeed
php bin/hyperf.php seed:run --class SeoKeywordSeed
pm2 start start.sh