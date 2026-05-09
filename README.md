# WhoseTurn

> App #01/52 — [The Forge Agency](https://the-forge.agency) sprint challenge

**WhoseTurn** est une web app de rotation automatique des tâches ménagères en colocation. Zéro inscription, accès uniquement par lien partagé.

## Le concept

En coloc, il y a toujours quelqu'un qui fait tout... et quelqu'un qui ne voit rien. WhoseTurn résout ça avec une rotation automatique, équitable et sans prise de tête.

- Crée ta coloc en 30 secondes
- Ajoute tes colocs avec avatar ou photo
- Choisis les tâches (ou crée les tiennes)
- Chaque semaine, la rotation change automatiquement
- Scanne un QR code sur le frigo pour voir qui fait quoi

## Stack technique

- **Backend** : Laravel 13 (PHP 8.5)
- **Frontend** : Blade + Tailwind CSS 4 + Alpine.js
- **Base de données** : SQLite
- **QR Codes** : simplesoftwareio/simple-qrcode
- **Tests** : Pest v4 (34 tests)

## Build in public — Comment ce projet a été créé

Ce projet a été entièrement construit avec **Claude Code** (Claude Opus 4) en une seule session de pair programming IA. Voici le process :

### Prompt initial

> "À l'aide du readme, analyse le brief, toutes les assets (les rename, mapper leurs noms et l'usage qu'on va en faire), fais-moi un plan ultra détaillé avec des tickets sous forme de .md [...] tu dois faire une application 100% fonctionnelle en Laravel, aucune feature n'est complexe, tout doit rester simple et efficace."

### Les étapes

1. **Analyse du brief** — Lecture du README + clone de la maquette React/Next.js pour extraire la direction artistique exacte (couleurs, fonts, composants)

2. **Mapping des assets** — 51 fichiers bruts (SVG, PNG) analysés, renommés et mappés :
   - 16 avatars PNG (`8.png`→`23.png` → `personnage-01.png`→`personnage-16.png`)
   - 10 icônes tâches SVG
   - 3 logos SVG

3. **Plan en 9 tickets** — Découpés dans `tickets/01-fondation.md` → `tickets/09-qr-codes.md`, chacun avec objectif, fichiers, tâches et critères d'acceptation

4. **Implémentation séquentielle** — Ticket par ticket, dans l'ordre chronologique logique :
   - 01: Fondation & Design System (Tailwind tokens, Alpine.js, layout Blade)
   - 02: Base de données & Modèles (4 migrations, 4 modèles, factories, seeder)
   - 03: Landing page + Routes (12 routes, formulaire création coloc)
   - 04: Onboarding en 2 étapes (wizard colocs + tâches)
   - 05: Dashboard + Rotation (algo round-robin + scoring)
   - 06: Completion dialog (bottom-sheet Alpine.js, 3 statuts)
   - 07: Page réglages (CRUD colocs/tâches)
   - 08: 404, polish & tests Pest (29 puis 34 tests)
   - 09: QR Codes (par coloc + par tâche, page imprimable)

5. **Itérations UX** — Ajustements post-feedback :
   - Bug toggle tâches (double-toggle `<label>` → `<div>`)
   - Locale FR complète (dates Carbon + messages validation)
   - Layout desktop responsive (grilles multi-colonnes)
   - Ajout tâches custom + suppression
   - Upload photo avatar (camera/galerie)
   - Accents français partout

### Allers-retours IA notables

| Problème | Prompt | Solution |
|---|---|---|
| `share_code` pas généré en seed | Constaté en tinker | Suppression du trait `WithoutModelEvents` dans le seeder |
| Migration `task_completions` échoue | FK vers `tasks` qui n'existe pas encore | Timestamp de migration décalé de 1 seconde |
| Tests unitaires sans DB | Tests dans `tests/Unit/` | Déplacés dans `tests/Feature/` (RefreshDatabase global) |
| Toggle ne toggle pas | Retour utilisateur "les activités sont pas décochables" | `<label>` + `@click` + `x-model` = double toggle → `<div>` avec `@click` seul |
| Dates en anglais | "il faut que l'app soit en français à 100%" | `APP_LOCALE=fr` + fichier `lang/fr/validation.php` complet |

### Prompts clés utilisés

```
"l'objectif est d'avoir un QR code de la coloc et un QR code par tâche 
pour vérifier qui doit faire quoi, exportable facilement"
```

```
"On doit aussi pouvoir mettre une photo de sa pellicule ou importer 
ou prendre une photo, il faut pouvoir rajouter des tâches custom 
également, comme 'sortir le chien'"
```

```
"fais attention, tu n'as pas mis un seul accent dans toute l'app"
```

## Installation

```bash
git clone git@github.com:The-Forge-Agency/WhoseTurn.git
cd WhoseTurn
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
composer dev
```

L'app tourne sur `http://localhost:8000`.

## Architecture

```
app/
├── Http/Controllers/
│   ├── ColocController.php      # Landing, dashboard, completion
│   ├── QrCodeController.php     # QR codes SVG + pages
│   ├── SettingsController.php   # CRUD colocs/tâches
│   └── SetupController.php      # Wizard onboarding
├── Models/
│   ├── Coloc.php                # share_code auto-généré
│   ├── Roommate.php             # avatar preset ou photo custom
│   ├── Task.php                 # défaut + custom, toggleable
│   └── TaskCompletion.php       # done / not_done / done_by_other
└── Services/
    └── RotationService.php      # Algo rotation + scoring
```

### Algorithme de rotation

- **Semaine active** : lun-ven = cette semaine, sam-dim = semaine prochaine (ISO week)
- **Rotation simple** : `(taskIndex + weekNumber) % roommateCount`
- **Avec scoring** : pondération basée sur les completions de la semaine précédente
  - `not_done` → -1 pour l'assigné (plus de tâches la semaine suivante)
  - `done_by_other` → -1 pour l'assigné, +1 pour l'aidant

## Tests

```bash
php artisan test --compact
# 34 tests, 34 passed, 79 assertions
```

## Design System

- **Fond** : `#FFF8F0` (cream)
- **Texte** : `#2D2D2D` (ink)
- **Accent** : `#FF6B6B` (coral)
- **Fonts** : Nunito (titres) + Space Mono (body)
- **Radius** : `rounded-2xl` partout

## Licence

MIT
