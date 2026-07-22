FROM docker.io/coollabsio/coolify:4.1.2
COPY socialite.php /var/www/html/bootstrap/helpers/socialite.php
COPY en.json /var/www/html/lang/en.json
COPY de.json /var/www/html/lang/de.json
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
COPY rebrand/OauthController.php /var/www/html/app/Http/Controllers/OauthController.php
COPY rebrand/2026_07_22_000000_vultify_rebrand_seeded_strings.php /var/www/html/database/migrations/2026_07_22_000000_vultify_rebrand_seeded_strings.php
COPY rebrand/TestNotification.php /var/www/html/app/Notifications/Test.php
COPY rebrand/emails-footer.blade.php /var/www/html/resources/views/components/emails/footer.blade.php
RUN find /var/www/html/resources/views -type f -name '*.blade.php' -exec sed -i 's/| Coolify/| Vultify/g' {} +
# Notification subjects/bodies (deployment success/failure, restart-limit,
# status-changed, API-token-expiring, container-restarted, ...) are plain
# PHP string literals, not Blade templates, so the pass above never touched
# them -- these were still emailing/Discord/Slack-posting "Coolify: ..." to
# users. Confirmed every match is inside a string literal (no namespace or
# class-name collisions) before doing a blanket replace across this directory.
RUN find /var/www/html/app/Notifications -type f -name '*.php' -exec sed -i 's/Coolify/Vultify/g' {} +
# German language pack: Coolify ships lang/de.json for the auth screens
# (login/register/reset-password) only -- the rest of the dashboard has no
# i18n coverage at all (hardcoded English strings in Blade templates), so
# "full German UI" isn't realistically achievable without patching dozens
# of upstream view files. This makes the part that IS translatable actually
# used: de.json above adds the missing "Login with Ares" key and fixes a
# leftover "coollabsio/coolify-examples" brand mention the earlier
# Coolify->Vultify sed pass never touched (it only covered *.blade.php and
# Notifications/*.php, not lang/*.json). config/app.php's locale is
# hardcoded (not env-driven), so it's patched directly here.
RUN sed -i "s/'locale' => 'en'/'locale' => 'de'/" /var/www/html/config/app.php
