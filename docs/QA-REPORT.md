# SiteDesk QA report

**Date:** 22 November 2026 · **Build tested:** `74f704b`, fixes shipped in `ffbd482` and `529d778`
**Environment:** Ubuntu 24.04 server (Nginx, PHP 8.4-FPM, MySQL 8, production `.env`, `APP_DEBUG=false`), plus a local dev copy for browser checks
**Result:** ✅ **Pass**: 9 issues found, all fixed and covered by regression tests. Automated suite: **30 tests, 0 failures**.

---

## 1. Summary of issues found

| # | Severity | Issue | Status |
|---|---|---|---|
| 1 | 🔴 High | **Contact form and staff login shared one rate limit.** `throttle:5,1` on both routes used a single counter per IP, so 5 contact messages from an office IP locked staff out of the admin login (HTTP 429). | Fixed: named limiters `contact` (5/min per IP) and `login` (5/min per email+IP, 20/min per IP) |
| 2 | 🟠 Medium | **`/blog?q[]=x` returned 500.** An array in the query string hit `(string)` casting ("Array to string conversion"). The same pattern existed in the admin post search/filter. | Fixed: `is_string()` guards; status filter whitelisted |
| 3 | 🟠 Medium | **Switching "Published" off didn't unpublish** when the (empty) schedule field was not sent. The post stayed public. | Fixed: `published_at` is now always set explicitly |
| 4 | 🟠 Medium | **Renaming a published post changed its URL**, so shared links and search results returned 404. | Fixed: slug is fixed once a post has been public (drafts still follow the title) |
| 5 | 🟠 Medium | **Contact form fields were pre-filled with "Monday – Friday, 9:00 – 18:00".** A `$value` loop variable on the page leaked into the shared form-field partial. | Fixed: loop variables renamed; regression test checks inputs start empty |
| 6 | 🟡 Low | Nginx `add_header` inside `location /build/` **dropped the security headers** for assets (inherited headers are replaced, not merged). | Fixed: removed the extra `add_header`; `expires 1y` kept |
| 7 | 🟡 Low | Blog list headings jumped from **H1 to H3** (screen-reader structure). | Fixed: cards render `h2` on the blog page |
| 8 | 🟡 Low | Small grey admin text (`slate-400` on white, ~2.6:1) **failed WCAG AA contrast**. | Fixed: `slate-500` (~4.8:1) |
| 9 | 🟡 Low | Cosmetic: file name under the image preview never showed; empty `favicon.ico` and no favicon on admin/login/error pages; `.env` returned 403 (reveals it exists); "Try a different word" shown on empty pages with no search; validation said "service id". | Fixed |

Not a bug: Thai text first appeared as `?????` in the database. It was caused by the Windows test shell passing the text to `curl`. The same request sent from Linux stored and displayed Thai correctly (verified in MySQL, the admin panel and search).

---

## 2. Functional tests

### Public site
| Test | Result |
|---|---|
| Home, blog, post, contact, login pages return 200 | ✅ |
| Unknown page / post → custom 404 page | ✅ |
| Drafts and scheduled posts are hidden from the public (404, not in list) | ✅ |
| Blog search finds posts; empty results show a helpful message | ✅ |
| Out-of-range page (`?page=99`), non-numeric page, malformed query → no errors | ✅ (after fix #2) |
| Mobile layout at 375 px: no horizontal scroll, menu opens/closes | ✅ |

### Contact form
| Test | Result |
|---|---|
| Valid message → saved, success screen, owner notification queued and sent by worker | ✅ |
| Empty required fields → field-level error messages | ✅ |
| Invalid email, message < 10 or > 3000 characters → rejected | ✅ |
| Invalid / hidden service id → rejected with friendly message | ✅ |
| Honeypot field filled (bot) → rejected, nothing stored | ✅ |
| Missing CSRF token → 419 "Page expired" page | ✅ |
| 6th message in one minute → 429 "Slow down" page | ✅ |
| Staff can still log in after the contact limit is hit | ✅ (after fix #1) |
| Thai text (name, message, search) | ✅ |

### Admin panel
| Test | Result |
|---|---|
| Guests are redirected to `/login` from every admin page | ✅ |
| Wrong password → error; correct → dashboard; logout → session ended | ✅ |
| Create published post with cover image → visible on blog, image served | ✅ |
| Create scheduled post / draft → not public | ✅ |
| Replace cover → old file deleted from disk | ✅ |
| Delete post → cover file deleted, styled confirm dialog shown first | ✅ |
| Unpublish post → no longer public | ✅ (after fix #3) |
| Rename published post → old URL still works | ✅ (after fix #4) |
| Upload a PHP file renamed to `.png` → rejected ("must be an image") | ✅ |
| Upload > 2 MB → rejected | ✅ |
| Hide service → removed from home page and contact dropdown; re-enable restores it | ✅ |
| Open inquiry → marked as read, unread badge decreases | ✅ |
| Toast notifications after save/delete; auto-dismiss | ✅ |
| Drag-and-drop image preview, character counter, auto-growing textarea, loading state on submit | ✅ |

---

## 3. Security tests

| Test | Result |
|---|---|
| XSS: `<script>` and `<img onerror>` submitted via the contact form are escaped on the dashboard, inbox and detail page | ✅ |
| XSS in search query is escaped | ✅ |
| SQL injection-style search (`' OR 1=1--`) → normal empty result (Eloquent bindings) | ✅ |
| `/.env`, `/.git/config`, `/storage/../.env` → not served (404) | ✅ |
| PHP files inside `/storage` are never executed | ✅ |
| Wrong HTTP method (e.g. `DELETE /blog`) → 405 | ✅ |
| Session cookie `HttpOnly` + `SameSite=Lax`; encrypted sessions | ✅ |
| Security headers: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`; server version hidden | ✅ |
| Login brute force: 5 attempts/min per email+IP | ✅ |
| `APP_DEBUG=false`: errors show a friendly page, no stack traces | ✅ |

---

## 4. Accessibility

Automated audit of 12 pages (public + admin): every page has `lang` and a title, **all inputs have labels**, **all images have alt text**, all buttons/links have accessible names, one `h1` per page, no duplicate IDs, correct heading order (after fix #7). Keyboard navigation works with a visible focus outline; there's a "Skip to content" link; animations respect `prefers-reduced-motion`.

---

## 5. Performance (production server, warm cache)

| Page | Time to first byte | Size (gzip) |
|---|---|---|
| Home | ~33 ms | 8.2 KB |
| Blog | ~42 ms | 4.4 KB |
| Article | ~30 ms | 4.6 KB |
| Contact | ~34 ms | 4.0 KB |

CSS 12 KB and JS 1.8 KB (gzip), cached for 1 year (hashed file names). 20 concurrent requests: all 200, average 27 ms, max 45 ms. OPcache enabled, config/routes/views cached at deploy.

---

## 6. Recommendations (not blocking)

1. **SMTP:** the demo server logs emails instead of sending them; add real SMTP credentials before going live.
2. **HTTPS:** add a domain and run Certbot, then set `SESSION_SECURE_COOKIE=true`.
3. **Off-site backups:** configure the `rclone` remote so nightly backups leave the server.
4. **Content Security Policy** header as an extra XSS defence layer.
5. **Uptime monitoring** (e.g. a free external ping on `/up`).
