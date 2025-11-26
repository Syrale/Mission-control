# Changelog

All notable changes to this project will be documented in this file.

The format is based on "Keep a Changelog" and this project adheres to Semantic Versioning.  
See: Keep a Changelog and SemVer for guidance on structure and version bumps. 

---

## [Unreleased]
- (Add planned changes here before cutting the next release)

---

## [0.5.1] - 2025-11-24
### Added
- Notes / Scratchpad Widget: Restored the missing "Notes" section to the Game Details view.
  - Implemented as a collapsible accordion in the "Tools" column.
  - Added hidden form fields to ensure compatibility with existing Controller validation rules.
  - Uses a monospaced font optimized for farm routes and coordinate tracking.

### Fixed
- UI Regression: Fixed an issue where the notes field was inadvertently removed during the games.show layout refactor.
- Timer UX: Improved the "Add Timer" interface with a toggle button (+ Add Timer / - Cancel) to reduce visual clutter when not in use.

### Changed
- Timer Input Mode: "Add Timer" form now defaults to "Duration Mode" (Days/Hours) instead of "Date Mode" for faster data entry.
- Visual Hierarchy: Reorganized the "Tools & Widgets" column order to: Add Task → Timers → Maintenance → Notes.

---

## [0.5.0] - 2025-11-24
### Refactored
- Dashboard Architecture: Major cleanup of the Dashboard View.
  - Separation of Concerns: Moved all complex calculation logic (sorting, alerts, schedule generation) out of Blade files and into the `DashboardController`.
  - Modular Views: Split the massive `dashboard.blade.php` file into a clean Index file and reusable Partials (`partials/header`, `alerts`, `games-grid`, `schedule`, etc.).
  - Performance: Improved rendering efficiency by pre-calculating calendar data in the Controller rather than looping inside the View.

### Internal
- Code Quality: Adopted "Level 2" Laravel Industry Standards by utilizing View Composers / Controller Data Preparation instead of logic-heavy Blade templates.

---

## [0.4.1] - 2025-11-24 (Hotfix)
### Fixed
- Database Schema: Added missing `next_due_at` column to `tasks` table. This fixes the `General error: 1` crash when creating new tasks.
- Task Recalculation: Tasks now automatically recalculate their due dates when Game timezone or reset hour settings are updated.
- Stale Timestamps: Fixed issue where changing a game's reset time (e.g., from 04:00 to 20:00) would not update existing task deadlines, causing incorrect UTC times to display on the dashboard.

### Changed
- Task Model: Added `recalculateDueAt()` method to dynamically update task due dates based on current game settings.
- GameController: Now detects changes to `timezone` or `reset_hour` and triggers automatic task recalculation on save.
- Task Fillable: Added `next_due_at` and `last_reset_date` to the `$fillable` array for proper mass assignment.

---

## [0.4.0] - 2025-11-24
### Added
- Missed Tasks Alert: Red notification box now appears when daily/weekly tasks remain incomplete past their reset time.
- Maintenance Countdown: Game cards now display a live countdown timer showing time remaining until maintenance ends.
- Pending Tasks Warning: During maintenance, cards display a warning badge showing how many incomplete tasks are waiting.
- Enhanced Maintenance UI: Maintenance screens now show both countdown and pending task count in both Grid and List views.
- Smart Time Converter (Settings Page): The Game Configuration screen now displays real-time calculations showing what the Server Reset time will be in your local timezone.

### Changed
- Missed Tasks Alert: Now collapsible and dismissible. Users can minimize the alert with a close button. Clicking the header expands/collapses the list.
- Time Display Logic (Settings): Replaced static "Reset happens at X:00" with dynamic calculation showing "When server hits 4:00, it will be 9:00 PM for you."
- Improved Clarity: Added timezone offset calculations to prevent confusion between Server Time and Local Time.

### Fixed
- Missed Tasks Detection: Fixed logic to properly detect overdue tasks by comparing current time against today's reset time (not next reset).
- Timezone Handling: Improved missed task detection to respect game-specific timezones.
- Settings Page UX: Fixed confusing time display where reset hour just echoed the input value without showing local conversion.

---

## [0.3.2] - 2025-05-23 (Hotfix)
### Fixed
- Database Schema: Added missing `type` column to `game_events` table via migration. This fixes the `General error: 1` crash when creating new events.
- Refinement: Existing events will default to `type = 'event'` to prevent data inconsistencies.

---

## [0.3.1] - 2025-05-23
### Added
- Tiered Alert System: Revamped the "Upcoming Events" section into three distinct priority tiers:
  - Focus/Critical (< 6 Hours) — Red, pulsing UI for immediate deadlines.
  - Urgent (< 24 Hours) — Amber UI for events ending today.
  - Upcoming (< 3 Days) — Blue list view for planning ahead.
- Event UI: Added distinct icons (Fire, Hourglass, Calendar) to differentiate alert tiers.
- Animation: Added "Pulse" animation to critical event alerts to draw attention.

### Fixed
- Event Logic: Fixed a bug where "1 week left" events appeared in critical alerts by switching date comparison logic from `diffInHours` to `lte` (Less Than or Equal).
- Model Security: Added `type` to the `GameEvent` model's `$fillable` array.

---

## [0.3.0] - 2025-05-22
### Added
- Dual Timezone Display: Tasks on the dashboard now display both Server Time (UTC) and the user's Local Browser Time simultaneously.
- Dashboard Layouts: Added a new "Grid / Card" view for games alongside the existing "List / Accordion" view.
- View Preferences: Users can now toggle between "Grid Cards", "Compact List", and "Calendar" modes. Preference is saved to local storage.
- Visual Alerts:
  - Added Blinking/Pulse animation to Game Cards (Grid) and Rows (List) when a game is in Active Maintenance.
  - Added Amber/Orange styling to make maintenance mode immediately obvious.
  - Grid View now hides the task list and shows a "Servers Offline" placeholder during maintenance.
- Navigation: Added a "Layout" section to the user dropdown menu to easily switch views.

### Changed
- Refactor: Moved all Dashboard logic from `routes/web.php` into a dedicated `DashboardController`. This centralizes business logic and simplifies views.
- Task Logic: `TaskController` now defaults task priority to 'medium' if not specified.
- Database: Renamed maintenance columns in migration from `start_time`/`end_time` to `start_at`/`end_at` to align with Laravel conventions and Controller logic.

### Fixed
- Maintenance Creation: Fixed a `QueryException` where maintenance records failed to save due to column name mismatch (`start_at` vs `start_time`).
- Dashboard Alerts: Fixed logic ensuring the "Missed Deadlines" alert properly receives the `$missedTasks` variable from the controller.
- Bug: Fixed an issue where creating a task would silently fail due to missing priority validation.

---

## [0.2.0] - 2025-05-21
### Added
- Maintenance Mode: Ability to schedule maintenance (Start/End times).
- Dashboard Alerts:
  - "Active Maintenance" alert (Amber) when servers are down.
  - "Maintenance Complete" alert (Green) for 24 hours after maintenance ends.
  - "Missed Deadlines" alert (Red) when tasks are overdue.
- Task Priorities: Added urgency logic (Critical < 3h, Warning < 24h).

### Changed
- Task Sorting: Dashboard now prioritizes games with tasks due soonest.
- Database: Added `start_at` and `end_at` columns to `maintenances` table.

---

## [0.1.1] - 2023-10-27
### Added
- Timezone Calculator: Real-time clock widget in "Add/Edit Game" forms.
- UI Indicators: "Maintenance Mode" (🔧) indicator on Game Cards.

### Fixed
- Fixed games in maintenance disappearing from dashboard sorting.
- Fixed "Overdue" calculation logic.

---

## [0.1.0] - 2023-10-20
### Added
- Initial Release:
  - Basic Game CRUD.
  - Task creation (Daily, Weekly, Loop).
  - Basic Dashboard with Timezone clocks.