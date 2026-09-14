# Nawasara Docs

Internal Nawasara documentation, living inside the app at `/nawasara-docs`.

Its content is read from the running code, not from separate notes. The endpoint list comes from the route table, the scope list from the registry, the component catalog from the `nawasara/ui` blade files, and the guides from markdown files already in the repo. Documentation that copies its source goes stale without anyone noticing; documentation that reads from the source cannot.

## Contents

| Page | Source |
|---|---|
| Component catalog | scans `packages/nawasara-ui/resources/views/components/**` |
| API reference | `Route::getRoutes()` plus `ScopeRegistry::grouped()` |
| Authentication and tokens | written by hand, explains the flow rather than listing data |
| Install the secscan agent | `packages/nawasara-secscan/README.md` from line 132 |
| Creating a new package | `AGENTS.md` |

The API page also flags two things that raise no error anywhere: a scope a route uses but that has not been registered (the endpoint is locked for all tokens), and a registered scope that no route uses (a middleware was probably left off).

## Access

Behind `auth` and the `docs.page.view` permission. It appears in the sidebar under the **Pengaturan** group.

```bash
php artisan db:seed --class="Nawasara\Docs\Database\Seeders\PermissionSeeder"
```

Only `view`, there is no create/update/delete. Documentation content is built from files and the runtime catalog, not from a database, so nothing can be edited through the panel; a write permission would only gate a page that does not exist.

The seeder grants this permission to every existing role, not just `developer`. Documentation is how you use the system, and gating it to developers only takes the guide away from the people who need it most: OPD operators new to the system. What changed is that the permission can now be revoked per role.

## Notes

Seed before deploying this version. The menu previously used `permission => null`, so every account holder saw it. Without the seeder, the Documentation workspace disappears from everyone's sidebar, because `WorkspaceManager::accessible()` filters by a permission that does not exist yet.

Gating happens in two places, and both are needed: `config/menu.php` hides the menu, and `routes/web.php` rejects the URL. Hiding the menu alone still leaves the address typeable directly.

## Notes for changes

The catalog is cached for 5 minutes. While editing a component, call `POST /nawasara-docs/refresh` or run `php artisan cache:clear` so changes show up right away.

Component documentation is not uniform. Some files have a `{{-- --}}` block at the top with a `Pemakaian:` section, some have only per-prop comments, some have none. The catalog page flags the undocumented ones instead of hiding them, so the list doubles as a task list. The most complete format is in `components/badge.blade.php`; follow that.

Tailwind needs two `@source` lines. In `resources/css/app.css` this package is registered via `packages/` and via `vendor/`. The first is for local dev, because `vendor/nawasara/docs` on Windows is a junction and Tailwind does not follow it; the second is for the build server, where `packages/` is empty.

Docs pages are static views, not Livewire. The `x-nawasara-ui::form.*` components rely on a Livewire context and will throw `Using $this when not in object context` if used here. Use native elements for simple needs like a client-side search box.

## Adding a page

1. Create a blade in `resources/views/pages/`
2. Register the route in `routes/web.php` inside the existing group. That group already carries `auth` plus `docs.page.view`, so a new page is gated too without having to remember it each time.
3. Add it to `src/Support/DocsNavigation.php`. The docs sidebar and the index page both read from there, so one place is enough.
4. If it needs to appear in the app sidebar, add it to `config/menu.php` as well.

## Author

**Pringgo J. Saputro** &lt;odyinggo@gmail.com&gt;

## License

MIT
