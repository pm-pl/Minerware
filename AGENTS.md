# AGENTS.md

PocketMine-MP 5.x plugin (virion): Minerware, a CubeCraft-inspired microgames minigame. Main class is `LatamPMDevs\minerware\Minerware` (`plugin.yml` → `src/LatamPMDevs/minerware/Minerware.php`). Requires PHP 8.0+, PM API 5.0.0.

## Setup / dependencies
- All virions are **virion v3** and declared in `composer.json` (no `.poggit.yml`). Run `composer install` before working. `vendor/` is gitignored.
- Virion deps: Commando (`cortexpe/commando` `dev-master` → LatamPMDevs fork), ConfigUpdater (`ifera-mc/config-updater`, an inline `repositories` package entry), FormAPI, languages, fakeblocks, libasynql (`^4.2.3`), ScoreFactory. When adding/removing a virion, edit `composer.json` and regenerate `composer.lock` via `composer update`.
- Commando's `dev-ready` branch conflicts with fakeblocks (different `muqsit/simple-packet-handler` refs); use `dev-master`.
- `phpstan.neon.dist` is a template; copy it to `phpstan.neon` to enable analysis (neon files are gitignored).

## Verification (no unit tests in this repo)

- Code style: `php-cs-fixer fix src/` using root `.php-cs-fixer.php`. The CI auto-commits style fixes, so match it locally. Style: tabs for indentation, `declare(strict_types=1)`, fully-imported native functions/classes (no `\` prefixes in code), ordered imports, no closing `?>`.
- Static analysis: copy `phpstan.neon.dist` → `phpstan.neon`, then `composer install` and `vendor/bin/phpstan.phar analyze --no-progress`.
- To ship a release build, compile virions into the plugin with `vendor/bin/pharynx` (scans virion deps from `composer.json`).
- CI PHP versions differ: `ci.yml` (php-cs-fixer) uses PHP 8.0; `phpstan.yml` uses the 8.2 PocketMine binaries.

## Architecture / conventions

- Every PHP source file opens with a large ASCII-art LGPL banner header — replicate it in any new file.
- Singletons via `SingletonTrait`: `Minerware`, `DataManager`, `ArenaManager`, `MicrogameManager`. Access with `X::getInstance()`.
- Adding a microgame: extend abstract `Microgame` in `src/LatamPMDevs/minerware/arena/microgame/` (`normal/` or `boss/` subdir), implement `getName`, `getLevel`, `getGameDuration`, `getRecompensePoints`, `tick`. Register it in `MicrogameManager::__construct` with a lowercase `saveName` key. Mark `[x]` in `Microgames.md` when complete.
- `Microgame::addWinner`/`addLoser` fire events (`PlayerWinMicrogameEvent`/`PlayerLoseMicrogameEvent`); use those to attribute points rather than mutating winner/loser state directly.
- `@phpstan-param` / `@phpstan-return` docblocks annotate `class-string<T>` templates (PHPStan level 6, paths = `src`).
- Maps: the platform must be exactly `24x24` (`Utils::calculateSize` enforces this). Map JSON data is saved under the plugin data folder `database/maps/`; configured interactively via chat commands during `/minerware arenas create`.
- `Arena` and `MapRegisterer` implement `Listener` and register events themselves; don't duplicate registration.

## Runtime / resources

- Player-facing text is localized via `.ini` files in `resources/languages/` (saved into the plugin data folder on load). Access through `Translator` (from `Minerware::getInstance()->getTranslator()`); the config `default-language` selects the locale.
- Database via libasynql; schemas live in `resources/database/sqlite.sql` and `mysql.sql`.