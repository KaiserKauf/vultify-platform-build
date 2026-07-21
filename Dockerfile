FROM docker.io/coollabsio/coolify:4.1.2
COPY socialite.php /var/www/html/bootstrap/helpers/socialite.php
COPY en.json /var/www/html/lang/en.json
COPY rebrand/login.blade.php /var/www/html/resources/views/auth/login.blade.php
COPY rebrand/register.blade.php /var/www/html/resources/views/auth/register.blade.php
COPY rebrand/forgot-password.blade.php /var/www/html/resources/views/auth/forgot-password.blade.php
COPY rebrand/reset-password.blade.php /var/www/html/resources/views/auth/reset-password.blade.php
COPY rebrand/two-factor-challenge.blade.php /var/www/html/resources/views/auth/two-factor-challenge.blade.php
COPY rebrand/confirm-password.blade.php /var/www/html/resources/views/auth/confirm-password.blade.php
COPY rebrand/navbar.blade.php /var/www/html/resources/views/components/navbar.blade.php
COPY rebrand/base.blade.php /var/www/html/resources/views/layouts/base.blade.php
COPY rebrand/coolify-logo.svg /var/www/html/public/coolify-logo.svg
COPY rebrand/app.blade.php /var/www/html/resources/views/layouts/app.blade.php
COPY rebrand/web.php /var/www/html/routes/web.php
COPY rebrand/Ares.php /var/www/html/app/Livewire/Ares.php
COPY rebrand/ares.blade.php /var/www/html/resources/views/livewire/ares.blade.php
COPY rebrand/AresStatus.php /var/www/html/app/Livewire/AresStatus.php
COPY rebrand/ares-status.blade.php /var/www/html/resources/views/livewire/ares-status.blade.php
COPY rebrand/dashboard.blade.php /var/www/html/resources/views/livewire/dashboard.blade.php
COPY rebrand/SystemLogs.php /var/www/html/app/Livewire/SystemLogs.php
COPY rebrand/system-logs.blade.php /var/www/html/resources/views/livewire/system-logs.blade.php
RUN find /var/www/html/resources/views -type f -name '*.blade.php' -exec sed -i 's/| Coolify/| Vultify/g' {} +
