# SymfonyDomainDrivenDesign

Przykładowa aplikacja **Symfony 6.4** zorganizowana według **Domain-Driven Design (DDD)** i podziału na **ograniczone konteksty (bounded contexts)**.

## Czym jest Domain-Driven Design (DDD)?

**Domain-Driven Design** to podejście do modelowania oprogramowania, w którym **domena biznesowa** (reguły, pojęcia i procesy z rzeczywistości problemu) stoi w centrum architektury. Kluczowe idee:

- **Ubiquitous Language (wszechobecny język)** — ten sam słownik pojęć w kodzie, rozmowach z domeną i dokumentacji, aby zredukować nieporozumienia między biznesem a IT.
- **Bounded Context (ograniczony kontekst)** — wyraźna granica modelu: wewnątrz kontekstu pojęcia mają jedno znaczenie; poza kontekstem ten sam termin może oznaczać coś innego. Konteksty komunikują się przez jasno zdefiniowane kontrakty (np. zdarzenia, API, komunikaty).
- **Warstwy** — typowo oddziela się **model domenowy** (encje, agregaty, wartości, polityki) od **aplikacji** (przypadki użycia, komendy, zapytania) oraz **infrastruktury** (baza, integracje, framework).
- **Agregaty i spójność** — grupy obiektów domenowych aktualizowane transakcyjnie, z jednym „korzeniem agregatu” jako punktem wejścia.

DDD nie jest „folderami w projekcie”, lecz sposobem myślenia o granicach modelu i o tym, co jest „rdzeniem” systemu a co techniczą.

## Jak to zostało odwzorowane w Symfony?

Struktura katalogów pod `src/Contexts/`:

| Kontekst   | Zawartość (przykład) |
|-----------|----------------------|
| **Shared** | Wspólne typy domenowe (`Email`), znaczniki komend/zapytań, proste zdarzenia bazowe, infrastruktura pomocnicza, endpoint `/health`. |
| **Identity** | Rejestracja użytkownika: agregat `User`, `UserId`, zdarzenie `UserRegistered`, `UserRepositoryInterface`, komenda `RegisterUserCommand`, zapytanie `GetUserByEmailQuery`, implementacja Doctrine, kontroler HTTP i komenda CLI. |

W obrębie każdego kontekstu zastosowano warstwy:

- **`Domain/`** — model (agregaty, obiekty wartości, zdarzenia domenowe, interfejsy repozytoriów).
- **`Application/`** — logika przypadków użycia: handlery i **komendy/zapytania**; usługa `IdentityApplicationService` jako wejście do kontekstu z poziomu UI.
- **`Infrastructure/`** — m.in. persistence (Doctrine).
- **`UserInterface/`** — kontrolery HTTP, komendy konsoli.

**Symfony Messenger** służy jako **szyna komend i zapytań**: osobne magistrale `command.bus` (m.in. z middleware `doctrine_transaction`) i `query.bus`, handlery oznaczone `#[AsMessageHandler(bus: '...')]`.

**Doctrine ORM** mapuje wiele prefiksów (`Shared`, `Identity`) z wyłączonym `auto_mapping`, aby encje były przypisane do właściwego kontekstu.

Szczegóły konfiguracji: `config/packages/doctrine.yaml`, `config/packages/messenger.yaml`, `config/services.yaml`, `config/routes.yaml`.

## Stack technologiczny

| Obszar | Technologie |
|--------|-------------|
| Język / runtime | PHP **≥ 8.3** |
| Framework | **Symfony 6.4** (FrameworkBundle, Console, Runtime, Yaml, Dotenv) |
| Persystencja | **Doctrine ORM 3**, DBAL, **Doctrine Migrations** |
| Komunikacja w aplikacji | **Symfony Messenger** (magistrale command/query, transport synchroniczny) |
| Identyfikatory | **Symfony UID** |
| Hasła | **Symfony Password Hasher** (np. `NativePasswordHasher`) |
| Jakość kodu (dev) | **Psalm**, **psalm/plugin-symfony**, **PHP CS Fixer** |
| Narzędzia (dev) | **Symfony Maker Bundle** |

Opcjonalnie w repozytorium: **Docker Compose** (`compose.yaml`, `compose.override.yaml`) — do uruchomienia PostgreSQL lub innych usług lokalnie.

## Struktura projektu

Poniżej główne katalogi repozytorium (bez `vendor/`, generowanych plików w `var/cache` itp.):

```
.
├── .github/workflows/     # GitHub Actions (CI)
├── bin/                   # Punkt wejścia konsoli: console
├── config/                # Konfiguracja Symfony (bundles, services, routes, packages)
├── migrations/            # Migracje Doctrine (wynik bin/console make:migration)
├── public/                # Document root WWW (index.php)
├── src/
│   ├── Contexts/         # Bounded contexts (DDD)
│   ├── Controller/       # Puste miejsce / placeholder Symfony (logika HTTP w Contexts/*/UserInterface)
│   ├── Entity/           # Placeholder — encje domenowe leżą w Contexts/*/Domain
│   ├── Repository/       # Placeholder — repozytoria w Contexts/*/Infrastructure
│   └── Kernel.php        # Kernel aplikacji Symfony
├── var/                  # Cache Symfony, logi, pliki pomocnicze (katalog w .gitignore)
├── compose.yaml          # Docker Compose (opcjonalnie)
├── compose.override.yaml # Lokalne nadpisania Compose
└── (pliki główne: composer.json, psalm.xml, .php-cs-fixer.dist.php, .env*, README, LICENSE)
```

### Opis katalogów (poziom repozytorium)

| Katalog / plik | Rola |
|----------------|------|
| `.github/` | Definicje CI/CD (np. workflow sprawdzający styl i Psalm). |
| `bin/` | Skrypt `console` — wszystkie komendy `php bin/console …`. |
| `config/` | `bundles.php`, `services.yaml`, `routes.yaml`, `packages/*.yaml` — DI, routing, Messenger, Doctrine itd. |
| `migrations/` | Wersjonowany schemat bazy (Doctrine Migrations). |
| `public/` | Katalog serwowany przez serwer HTTP; jedyny publiczny front kontrolowany przez framework. |
| `src/` | Kod aplikacji (poniżej szczegóły `Contexts/`). |
| `var/` | Artefakty środowiskowe: cache Symfony, logi, baza SQLite jeśli używasz lokalnie ścieżki pod `var/`. |
| `vendor/` | Zależności Composer (instalowane lokalnie, nie commitowane). |

### `src/Contexts/` — konteksty i warstwy

Każdy **bounded context** ma ten sam układ warstw:

```
src/Contexts/<NazwaKontekstu>/
├── Application/      # Komendy, zapytania, handlery, fasady aplikacyjne (np. IdentityApplicationService)
├── Domain/           # Model domenowy: agregaty, VO, zdarzenia, interfejsy repozytoriów
├── Infrastructure/   # Adaptery: Doctrine, integracje zewnętrzne, kod „przy frameworku”
└── UserInterface/    # Wejścia: Http/, CLI/ — kontrolery, komendy konsoli
```

| Ścieżka | Opis |
|---------|------|
| `Shared/Application/Command`, `…/Query` | Wspólne interfejsy znacznikowe dla komend i zapytań. |
| `Shared/Domain/` | Wspólne typy (np. `Email`, zdarzenia bazowe, kontrakty domenowe bez logiki Identity). |
| `Shared/Infrastructure/` | Wspólne adaptery infrastruktury (np. `KernelEnvironment`). |
| `Shared/UserInterface/Http/` | Wspólne endpointy techniczne (np. `/health`). |
| `Identity/Application/` | Przypadki użycia Identity: rejestracja (`RegisterUser`), odczyt użytkownika (`GetUserByEmail`). |
| `Identity/Domain/User/` | Agregat `User`, `UserId`, `UserRegistered`, `UserRepositoryInterface`. |
| `Identity/Infrastructure/Persistence/` | `DoctrineUserRepository` — implementacja portu repozytorium. |
| `Identity/UserInterface/Http/`, `…/CLI/` | API HTTP i komenda `identity:user:register`. |

### Konfiguracja jakości kodu (w katalogu głównym)

| Plik | Rola |
|------|------|
| `psalm.xml` | Analiza statyczna (Poziom błędów, plugin Symfony, cache w `var/cache/psalm`). |
| `.php-cs-fixer.dist.php` | Reguły formatowania kodu (m.in. @Symfony + `declare_strict_types`). |

## Wymagania

- PHP **8.3** lub nowszy (minimalna wersja wynika z `composer.lock`, m.in. Doctrine DBAL 4.x oraz narzędzia dev). Rozszerzenia: `ctype`, `iconv`, `dom`, `json`, `mbstring`; dla bazy: `pdo_pgsql` / `pdo_mysql` / `pdo_sqlite`.
- [Composer](https://getcomposer.org/)
- Baza danych zgodna z `DATABASE_URL` (np. PostgreSQL, SQLite do szybkich testów).

## Uruchomienie projektu

### 1. Zależności

```bash
composer install
```

### 2. Zmienne środowiskowe

Skopiuj lub dostosuj `.env` / utwórz `.env.local` (nie commituj sekretów). Ustaw m.in.:

- `APP_SECRET`
- `DATABASE_URL` — np. SQLite:
  ```env
  DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
  ```
  lub PostgreSQL wg szablonu w `.env`.

### 3. Schemat bazy

```bash
php bin/console doctrine:schema:create
```

Albo migracje, jeśli są generowane:

```bash
php bin/console doctrine:migrations:migrate
```

### 4. Serwer deweloperski

Z [Symfony CLI](https://symfony.com/download):

```bash
symfony server:start
```

Lub wbudowany serwer PHP:

```bash
php -S localhost:8000 -t public
```

Domyślnie aplikacja odpowiada pod `public/`.

### 5. Przykładowe endpointy

| Metoda | Ścieżka | Opis |
|--------|---------|------|
| `GET` | `/health` | Status techniczny (Shared) |
| `POST` | `/identity/register` | JSON: `{"email":"...","password":"..."}` |
| `GET` | `/identity/users/by-email?email=...` | Podgląd użytkownika po e-mailu |

### 6. Konsola (Identity)

```bash
php bin/console identity:user:register user@example.com haslo
```

### Jakość kodu (lokalnie)

```bash
composer psalm
composer cs-fix      # poprawki stylu
composer cs-check    # tylko weryfikacja (np. CI)
```

## CI

Workflow **GitHub Actions**: `.github/workflows/ci.yml` (m.in. `composer install`, PHP CS Fixer dry-run, Psalm, lint kontenera i YAML).

## Licencja

Copyright © **Maxsoft**. Wszelkie prawa zastrzeżone. Szczegóły w pliku [LICENSE](LICENSE).
