# Flarum Development Commands

## Extension Management
php flarum extension:enable nippytime-post-pin-flarum
php flarum extension:disable nippytime-post-pin-flarum
php flarum extension:list

## Cache Management
php flarum cache:clear
php flarum info

## Database
php flarum migrate
php flarum migrate:reset
php flarum migrate:status

## Assets
php flarum assets:publish

## Development Workflow
1. Make changes to extension
2. Run: npm run build (in extension directory)
3. Run: composer update nippytime/post-pin-flarum (in Flarum directory)
4. Run: php flarum cache:clear
5. Test in browser

## Useful URLs
- Forum: http://localhost/my-flarum-test
- Admin: http://localhost/my-flarum-test/admin
- API: http://localhost/my-flarum-test/api
- phpMyAdmin: http://localhost/phpmyadmin