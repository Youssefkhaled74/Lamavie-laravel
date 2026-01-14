# AI Onboarding — Lamavie Backend

Purpose
- Provide a single, professional onboarding file for AI agents interacting with this repository.
- Include concise project context, usage rules, prompt style, and expectations for code changes.

Quick facts (always read first)
- Project: Lamavie backend (Laravel 12)
- PHP: ^8.2
- Key folders: `app/`, `routes/`, `resources/views/`, `database/` (migrations, seeders), `config/`.
- Auth guards: `admin`, `driver`, `lab`, `user` (routes often use `auth:admin`, `auth:driver`, `auth:lab`).

Rules for AI agents
1. Read `project_overview_and_maintenance_guide.md` first (canonical project summary).
2. When a user asks for code changes, respond with:
   - A short plan (1–3 steps).
   - A minimal, focused patch using unified diff format suitable for `apply_patch`.
   - If creating new files, include full path and content.
3. Prefer surgical edits: modify the smallest number of files, keep style consistent, and avoid sweeping refactors unless requested.
4. Add or update tests when the change affects behavior; include instructions to run tests.
5. When proposing new dependencies, include reason and composer/npm commands.

Patch & code style expectations
- Follow existing repository conventions (PSR-12, `app/` structure). Prefer `app/Services/` for business logic.
- Use Form Requests for validation and keep controllers thin.
- Migrations must be reversible and idempotent.

Prompt snippet to prepend (optional Arabic example)
- English: "Read `project_overview_and_maintenance_guide.md` first. Provide a short plan, then a minimal patch. Keep controllers thin and prefer services. Add tests where applicable."
- Arabic example (if user supplies):
  "aba 3ayez el file dh yb2aa feh prompt keda ab3ato daymna w ana batlob ay haba mn el ai agent 3shan yb2aa fahem el project w howa shaghalw kaman ekteb en dh laravel 12 mafesh kernal w zabata keda el prompt dh\n\nاكتب: ده Laravel 12 — مافيش Kernel و زبطها كده."

How to return patches
- Use the repository `apply_patch` format for edits.
- Include an explanation sentence or two after the patch describing what changed and why.

Operational notes
- Do not run commands in the user's environment. Provide exact commands for the user to run locally.
- When uncertain about runtime behavior, list assumptions clearly and request permission to run tests/migrations.

End of onboarding.
