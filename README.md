# WhoseTurn

> App #01/52 — [The Forge Agency](https://the-forge.agency) sprint challenge

**WhoseTurn** est une web app de rotation automatique des tâches ménagères en colocation. Zéro inscription, accès uniquement par lien partagé.

## Le concept

En coloc, il y a toujours quelqu'un qui fait tout... et quelqu'un qui ne voit rien. WhoseTurn résout ça avec une rotation automatique, équitable et sans prise de tête.

- Crée ta coloc en 30 secondes
- Ajoute tes colocs avec avatar ou photo
- Choisis les tâches (ou crée les tiennes avec photo custom)
- Règle la fréquence : chaque semaine, tous les 15 jours, 1x/mois
- Ajoute des tâches urgentes ponctuelles ("les poubelles débordent !")
- Chaque semaine, la rotation change automatiquement
- Consulte l'historique et les stats de chacun
- Scanne un QR code sur le frigo pour voir qui fait quoi
- Imprime les QR codes avec un rendu print CSS propre

## Stack technique

- **Backend** : Laravel 13 (PHP 8.5)
- **Frontend** : Blade + Tailwind CSS 4 + Alpine.js
- **Base de données** : SQLite
- **QR Codes** : simplesoftwareio/simple-qrcode
- **Tests** : Pest v4 (40+ tests)
- **Scheduler** : nettoyage automatique des tâches urgentes

## Build in public — Comment ce projet a été créé

Ce projet a été entièrement construit avec **Claude Code** (Claude Opus 4) en pair programming IA continu. Voici le process :

### Prompt initial

> "À l'aide du readme, analyse le brief, toutes les assets (les rename, mapper leurs noms et l'usage qu'on va en faire), fais-moi un plan ultra détaillé avec des tickets sous forme de .md [...] tu dois faire une application 100% fonctionnelle en Laravel, aucune feature n'est complexe, tout doit rester simple et efficace."

### Les étapes

1. **Analyse du brief** — Lecture du README + clone de la maquette React/Next.js pour extraire la direction artistique exacte (couleurs, fonts, composants)

2. **Mapping des assets** — 51 fichiers bruts (SVG, PNG) analysés, renommés et mappés :
   - 16 avatars PNG (`8.png`→`23.png` → `personnage-01.png`→`personnage-16.png`)
   - 10 icônes tâches SVG
   - 3 logos SVG

3. **Plan en 9 tickets** — Découpés avec objectif, fichiers, tâches et critères d'acceptation

4. **Implémentation séquentielle** — Ticket par ticket :
   - Fondation & Design System (Tailwind tokens, Alpine.js, layout Blade)
   - Base de données & Modèles (migrations, modèles, factories, seeder)
   - Landing page + Routes
   - Onboarding en 2 étapes (wizard colocs + tâches)
   - Dashboard + Rotation (algo round-robin + scoring)
   - Completion dialog (bottom-sheet Alpine.js, 3 statuts)
   - Page réglages (CRUD colocs/tâches)
   - 404, polish & tests Pest
   - QR Codes (par coloc + par tâche, page imprimable)

5. **Itérations post-feedback** — Ajustements en continu :
   - Bug toggle tâches (double-toggle `<label>` → `<div>`)
   - Locale FR complète (dates Carbon + messages validation)
   - Layout desktop responsive (grilles multi-colonnes)
   - Tâches custom (création + suppression + icône optionnelle + photo)
   - Upload photo avatar (camera/galerie)
   - Accents français partout
   - Fréquence par tâche (hebdo, bi-hebdo, mensuel)
   - Todo list urgente (tâches ponctuelles)
   - Page historique (4 dernières semaines)
   - Page statistiques (classement, répartition, taux de complétion)
   - QR codes améliorés (téléchargement, liens, print CSS A4)
   - Scheduler de nettoyage automatique

### Allers-retours IA notables

| Problème | Déclencheur | Solution |
|---|---|---|
| `share_code` pas généré en seed | Constaté en tinker | Suppression du trait `WithoutModelEvents` dans le seeder |
| Migration FK échoue | FK vers table pas encore créée | Timestamp de migration décalé |
| Tests unitaires sans DB | Tests dans `tests/Unit/` | Déplacés dans `tests/Feature/` (RefreshDatabase) |
| Toggle ne toggle pas | "les activités sont pas décochables" | `<label>` + `@click` + `x-model` = double toggle → `<div>` seul |
| Dates en anglais | "il faut que l'app soit en français à 100%" | `APP_LOCALE=fr` + `lang/fr/validation.php` |
| Avatar requis bloque photo | "je peux pas mettre de photo" | `avatar_slug` nullable + fallback validation |
| Tâches toutes cochées par défaut | "on peut tout laisser décocher" | `enabled: false` dans `createDefaultTasks()` |
| Icônes custom impossibles | "icone optionnel ou photo" | `icon_slug` nullable + `icon_url` + accessor `icon_src` |
| Pas de tâches ponctuelles | "comment je dis sortir les poubelles maintenant ?" | Table `urgent_todos` + todo list sur le dashboard |

### Prompts utilisateur clés

```
"l'objectif est d'avoir un QR code de la coloc et un QR code par tâche"
```

```
"on doit pouvoir mettre une photo, rajouter des tâches custom, 
comme 'sortir le chien' car jcp ça pourrait être le cas"
```

```
"comment marche la fréquence ? comment on fait quelque chose de ponctuel ? 
comment on dit 'il faut sortir les poubelles mtn car ça déborde'"
```

```
"j'aimerais une page de stats pour voir qui fait quoi, la répartition, 
un truc rigolo mais fonctionnel et logique"
```

## Installation

```bash
git clone git@github.com:The-Forge-Agency/WhoseTurn.git
cd WhoseTurn/WhoseTurnApp
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

### Scheduler (optionnel, pour le nettoyage auto)

```bash
# En production (cron)
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

# En dev
php artisan schedule:work
```

## Architecture

```
app/
├── Http/Controllers/
│   ├── ColocController.php      # Landing, dashboard, completion, todos, stats, historique
│   ├── QrCodeController.php     # QR codes SVG + pages scan
│   ├── SettingsController.php   # CRUD colocs/tâches + fréquences
│   └── SetupController.php      # Wizard onboarding
├── Models/
│   ├── Coloc.php                # share_code auto-généré
│   ├── Roommate.php             # avatar preset ou photo custom
│   ├── Task.php                 # défaut + custom, fréquence, icône optionnelle
│   ├── TaskCompletion.php       # done / not_done / done_by_other
│   └── UrgentTodo.php           # tâches ponctuelles urgentes
└── Services/
    └── RotationService.php      # Algo rotation + scoring + historique
```

### Algorithme de rotation

- **Semaine active** : lun-ven = cette semaine, sam-dim = semaine prochaine (ISO week)
- **Rotation simple** : `(taskIndex + weekNumber) % roommateCount`
- **Avec scoring** : pondération basée sur les completions de la semaine précédente
  - `not_done` → -1 pour l'assigné (plus de tâches la semaine suivante)
  - `done_by_other` → -1 pour l'assigné, +1 pour l'aidant
- **Fréquence** : les tâches bi-hebdo/mensuelles sont filtrées avant l'assignation

### Pages

| Route | Page |
|---|---|
| `/` | Landing — créer une coloc |
| `/{code}` | Dashboard — qui fait quoi cette semaine |
| `/{code}/setup` | Onboarding — ajouter colocs + choisir tâches |
| `/{code}/settings` | Réglages — gérer colocs, tâches, fréquences |
| `/{code}/stats` | Statistiques — classement, répartition, taux |
| `/{code}/history` | Historique — 4 dernières semaines |
| `/{code}/qr` | QR Codes — imprimer / télécharger |
| `/{code}/task/{id}` | Page scan QR — qui fait cette tâche |

## Tests

```bash
php artisan test --compact
```

## Design System

- **Fond** : `#FFF8F0` (cream)
- **Texte** : `#2D2D2D` (ink)
- **Accent** : `#FF6B6B` (coral)
- **Fonts** : Nunito (titres) + Space Mono (body)
- **Radius** : `rounded-2xl` partout

## Licence

MIT
