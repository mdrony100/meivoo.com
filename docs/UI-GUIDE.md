# Meivoo UI guide

## What changed

A design-system layer sits on top of the existing markup, so every Blade view
picks up the new look without being rewritten:

| File | Scope |
| --- | --- |
| `public/assets/dashboard/meivoo.css` | Member app: shell, sidebar, topbar, cards, buttons, forms, feed, chat, tables, empty states, dark mode |
| `public/assets/dashboard/meivoo.js` | Theme persistence, toasts, global search, autosize, scroll reveal, back-to-top |
| `public/assets/mv-site.css` | Public site, marketing navbar/hero CTAs, auth screens |
| `public/assets/mv-admin.css` | AdminLTE skin: sidebar, stat cards, tables, forms, login |

Load order matters: theme files are always included **after** the legacy
stylesheets (`dashboard/style.css`, `assets/style.css`, `adminlte/admin.css`).

## Design tokens

- Brand: violet `#7c3aed` to `#a855f7` to indigo gradient
- Ink `#14142b`, muted `#6f6c8a`, canvas `#f6f5fb`, border `#ece9f6`
- Display type: Plus Jakarta Sans. Body: Inter.
- Radius 14px cards / 10px controls, soft violet-tinted shadows

Dark mode is opt-in per browser, stored in `localStorage['meivoo.theme']`, and
applied before first paint to avoid a flash.

## New features

- **Global search** in the topbar (`GET /user/search?q=`) across members, posts and events, with `/` as the shortcut
- **Notification bell** (`GET /user/summary`) for unread messages, upcoming events and birthdays
- **Notification centre** at `/user/notifications`, grouped by day with type filters
- **Dark mode**, toasts, back-to-top, autosizing composers

## Demo data

`php artisan migrate:fresh --seed` creates:

- Admin: `arobil@gmail.com` / `12345678`
- Members: `user1@example.com`, `user2@example.com`, `user3@example.com` with password `password`
- "The Rahman Family": 4 generations, 12 members, posts, comments, likes, events, legacy names, message threads

## Schema repairs

Migrations that create `events`, `ai_photos`, tokens, attachments, family trees,
and legacy names adopt a table that already exists. They add missing columns
and do not drop or replace it.

`family_relationships` keeps its primary key and the `family_id` foreign key.
Missing `member_id` / `person_id` (and the related-person pair) are added and
backfilled only where the destination is null. `relation_type` is filled from
`husband` / `wife` when it is null. The old rebuild, which copied the table and
dropped the original, is gone.

The admin dashboard counts signups with MySQL `MONTH(created_at)`.

`php artisan migrate:fresh --seed` is for an empty local database. Production
uses `php artisan migrate --force` and does not seed.
