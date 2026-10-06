# Temu Estates: complete code pack

Everything from your requirements document is here:

PUBLIC SITE: Home, Buy, Rent, Commercial, Property details (gallery, video, floor plan, map,
amenities, WhatsApp / Call / Request viewing / Send inquiry), live search and filters, List
Your Property, About, Diaspora section, Contact, WhatsApp everywhere, mobile-first
black-and-gold design, sitemap, share previews, structured data for Google.

ADMIN (/admin): Properties (add, edit, delete, restore, change price, mark Sold / Rented /
Reserved, upload photos, video, floor plans, edit descriptions, feature on homepage, show or
hide), Leads and inquiries (with WhatsApp and Call buttons), Property submissions (review,
reject, or create a draft listing with the owner's photos in one click), Locations, Property
types (residential / commercial), Amenities, Website settings (phone, WhatsApp, email,
address, social links), and a dashboard with counts. New leads trigger an email to the admin.

---------------------------------------------------------------------------------------------

## 1. Install the packages (inside ~/projects/temu-estates)

    composer require livewire/livewire filament/filament
    composer require spatie/laravel-medialibrary spatie/laravel-sluggable
    composer require filament/spatie-laravel-media-library-plugin

Check your Filament version. This code is for Filament 4 or 5 (they use the same code):

    composer show filament/filament | head -3

## 2. Create the admin panel (skip if /admin already exists)

    php artisan filament:install --panels
    php artisan make:filament-user

## 3. Copy this pack into the project
From the folder where you unzipped it:

    cp -r app database resources routes tests public ~/projects/temu-estates/

Say yes to overwrite. (`routes/web.php` and `DatabaseSeeder.php` are meant to be replaced.)

## 4. Allow bigger photo uploads (Ubuntu allows only 2 MB by default)

    V=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
    sudo sed -i 's/^upload_max_filesize.*/upload_max_filesize = 64M/; s/^post_max_size.*/post_max_size = 70M/' /etc/php/$V/cli/php.ini
    grep -E "^(upload_max_filesize|post_max_size)" /etc/php/$V/cli/php.ini

## 5. Check .env
Open `.env` (nano .env) and make sure these lines exist:

    APP_URL=http://127.0.0.1:8000
    FILESYSTEM_DISK=public

## 6. Prepare the database

    php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
    php artisan storage:link
    php artisan migrate
    php artisan db:seed

## 7. Run the tests (they check the whole site)

    php artisan test

Everything should be green. If something is red, paste the message and we'll fix it.

## 8. Run the site

    php artisan serve

Website: http://127.0.0.1:8000         Admin: http://127.0.0.1:8000/admin

## 9. Your first 10 minutes in the admin
1. Website settings: replace the placeholder phone, WhatsApp (format 251911223344), email, address.
2. Properties: open "Luxury 3 Bedroom Apartment", upload photos, Save. Refresh the website.
3. Properties: Add property. Fill the form, switch on "Show on website".
4. Use the "Status" button on a row to mark a property Sold, Rented or Reserved.
5. Visit the website, send a viewing request, then see it under "Leads and inquiries".
6. Submit a property on the website, then find it under "Property submissions" and
   click "Create listing".
7. Delete the 5 other sample properties when you have your own.

## 10. Save your work

    git add .
    git commit -m "Complete Temu Estates website and admin"

---------------------------------------------------------------------------------------------

## Before going live (important)
- Admin access: edit `app/Models/User.php`. Add `implements \Filament\Models\Contracts\FilamentUser`
  and this method, so only approved emails can enter /admin:

      public function canAccessPanel(\Filament\Panel $panel): bool
      {
          return in_array($this->email, ['your-email@example.com']);
      }

- Email alerts: in `.env` set real MAIL_* details (MAIL_MAILER=smtp ...). Locally, emails are only
  written to storage/logs/laravel.log.
- Tailwind: the design loads Tailwind from a CDN for development. For production, run
  `npm install && npm run build` and replace the CDN <script> in
  `resources/views/components/layouts/app.blade.php` with `@vite(['resources/css/app.css'])`
  (we can do this together).
- Production .env: APP_ENV=production, APP_DEBUG=false, real APP_URL (https), a MySQL database.
- Hosting, HTTPS, backups: ask me for the deployment guide when you are ready.

## Notes
- The WhatsApp and phone numbers in the seeder are placeholders.
- Optional hero photo: put a wide image at `public/images/hero.jpg`.
- This code was written without being executed in my environment. The tests (step 7) are there
  so the first run shows any problem immediately.
