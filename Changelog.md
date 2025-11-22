# Changelog

All notable changes to this project will be documented in this file.

## [0.3.0] - 2025-05-22

### Added
- **Dual Timezone Display:** Tasks on the dashboard now display both Server Time (UTC) and the user's Local Browser Time simultaneously.
- **Dashboard Layouts:** Added a new "Grid / Card" view for games alongside the existing "List / Accordion" view.
- **View Preferences:** Users can now toggle between "Grid Cards" and "Compact List" modes. Preference is saved to local storage.
- **Visual Alerts:**
  - Added **Blinking/Pulse animation** to Game Cards (Grid) and Rows (List) when a game is in Active Maintenance.
  - Added **Amber/Orange styling** to make maintenance mode immediately obvious.
  - Grid View now hides the task list and shows a "Servers Offline" placeholder during maintenance.
- **Navigation:** Added a "Layout" section to the user dropdown menu to easily switch views.

### Changed
- **Refactor:** Moved all Dashboard logic from `routes/web.php` into a dedicated `DashboardController`. This cleans up the routes file and centralizes logic for missed tasks and maintenance checks.
- **Task Logic:** `TaskController` now defaults task priority to 'medium' if not specified.
- **Database Schema:** Renamed maintenance columns in migration from `start_time`/`end_time` to `start_at`/`end_at` to align with Laravel conventions and Controller logic.

### Fixed
- **Maintenance Creation:** Fixed a `QueryException` where maintenance records failed to save due to column name mismatch (`start_at` vs `start_time`).
- **Dashboard Alerts:** Fixed logic ensuring the "Missed Deadlines" alert (Section 2) properly receives the `$missedTasks` variable from the controller.
- **Bug:** Fixed an issue where creating a task would silently fail due to missing priority validation.

---

## [0.2.0] - 2025-05-21
### Added
- **Maintenance Mode:** Ability to schedule maintenance (Start/End times).
- **Dashboard Alerts:**
    - "Active Maintenance" alert (Amber) when servers are down.
    - "Maintenance Complete" alert (Green) for 24 hours after maintenance ends.
    - "Missed Deadlines" alert (Red) when tasks are overdue.
- **Task Priorities:** Added urgency logic (Critical < 3h, Warning < 24h).

### Changed
- **Task Sorting:** Dashboard now prioritizes games with tasks due soonest.
- **Database:** Added `start_at` and `end_at` columns to `maintenances` table.

---

## [0.1.1] - 2023-10-27
### Added
- **Timezone Calculator:** Real-time clock widget in "Add/Edit Game" forms.
- **UI Indicators:** "Maintenance Mode" (🔧) indicator on Game Cards.

### Fixed
- Fixed games in maintenance disappearing from dashboard sorting.
- Fixed "Overdue" calculation logic.

## [0.1.0] - 2023-10-20
### Initial Release
- Basic Game CRUD.
- Task creation (Daily, Weekly, Loop).
- Basic Dashboard with Timezone clocks.