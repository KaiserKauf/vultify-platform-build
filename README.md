# vultify-platform-build

Build context for the custom `vultify-platform` Docker image: a thin overlay on
top of `coollabsio/coolify:4.1.2` that adds

- Coolify → Vultify rebranding (login/register pages, navbar, page titles, logo)
- An "ares" Socialite OIDC provider so Vultify can federate login with
  [KaiserKauf/ares](https://github.com/KaiserKauf/ares) (see
  `ares/docs/superpowers/specs/2026-07-21-vultify-deep-auth-integration-design.md`
  in that repo for the full design)
- A full Ares AI panel (`/ares`) embedded via iframe, plus a live status
  widget on the main Coolify dashboard

No secrets are baked into the image — OIDC client secret, API keys, etc. are
all set as environment variables / DB rows on the running instance, never
here.

## CI

Every push to `main` builds and pushes to
`ghcr.io/kaiserkauf/vultify-platform:latest` (and `:<sha>`) via
`.github/workflows/build.yml`, using the GitHub Actions default token — no
manual registry credentials needed.

**One-time setup still needed:** the `gh` CLI token used in this session had neither
`write:packages` nor `read:packages` scope, so pushing had to go through Actions
(which gets its own scoped token automatically), but the resulting package is
still private and can't yet be pulled anonymously. Making the repo public did
NOT make the linked package public too (a known GitHub quirk — package
visibility doesn't follow repo visibility retroactively). To finish: go to
https://github.com/users/KaiserKauf/packages/container/vultify-platform/settings
and set visibility to Public — after that, `docker pull` on the Coolify host
needs no credentials at all. Until then, the host must stay `docker login`'d
to `ghcr.io` with a token that has `read:packages`.

## Deploying a new build

On the Coolify host running the `vultify-test` instance:

```sh
docker pull ghcr.io/kaiserkauf/vultify-platform:latest
docker tag ghcr.io/kaiserkauf/vultify-platform:latest vultify-platform:latest
docker compose -f docker-compose.yml -f docker-compose.prod.yml -f docker-compose.custom.yml up -d --no-deps coolify
docker exec coolify php artisan view:clear
docker exec coolify php artisan config:clear
```

`/data/coolify/source/docker-compose.custom.yml` overrides just the
`coolify` service's image — Coolify's own files are untouched.
