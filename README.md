# Ticket Tracker (Laravel)

A ticket tracking app built with Laravel, MySQL, Eloquent and Blade (Bootstrap).
Admins manage projects and users, Managers manage tickets inside their projects,
and Users work on the tickets assigned to them.

## Requirements

- PHP 8.4+
- Composer
- MySQL

## Setup

1. Clone the repository and install dependencies:

```bash
git clone https://github.com/nourghnimat-rgb/ticketTracker-laravel.git
cd ticketTracker-laravel
composer install
```

2. Create the environment file and generate the app key:

```bash
cp .env.example .env
php artisan key:generate
```

3. Create an empty MySQL database, then set your credentials in `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketTracker_laravel
DB_USERNAME=root
DB_PASSWORD=
```

4. Run the migrations and seed the first admin account:

```bash
php artisan migrate --seed
```

5. Start the app:

```bash
php artisan serve
```

Then open http://127.0.0.1:8000

## Default admin account

The seeder (`AdminSeeder`) creates the first admin. There is no public registration;
all other accounts are created by an Admin from the **Create User** page.

| Email             | Password     |
|-------------------|--------------|
| admin@example.com | Admin12345!  |

Change this password after the first login in any non-local environment.

## Queue worker

Notifications are sent through a queued job (`SendTicketNotification`) using the
`database` queue driver, so a worker must be running for notifications to be delivered.
Make sure `.env` has:

```
QUEUE_CONNECTION=database
```

Start the worker in a separate terminal and keep it running:

```bash
php artisan queue:work
```

If you change job or service code while the worker is running, stop it (Ctrl+C) and
start it again, because the worker loads the code only once.

The job is retried up to 3 times. If it still fails it is stored in the `failed_jobs`
table; list failures with `php artisan queue:failed` and retry them with
`php artisan queue:retry all`. If the ticket was deleted before the job ran, the job
is discarded silently.

## Scheduled command

The command `tickets:notify-overdue` finds tickets whose due date has passed and
dispatches the notification job to the assignee, the project's managers and all admins.
Tickets with status **Resolved** or **Closed** are skipped. It is scheduled to run daily
(`routes/console.php`).

Dates use the application timezone, which is `UTC` by default. Change `timezone` in
`config/app.php` if you want "today" and the daily run to follow your local time.

To see the schedule:

```bash
php artisan schedule:list
```

To simulate it locally, run the command directly (the queue worker must be running):

```bash
php artisan tickets:notify-overdue
```

To run the scheduler continuously on your machine (it checks every minute):

```bash
php artisan schedule:work
```

On a real server, run the scheduler with a single cron entry:

```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Roles and permissions

| Role    | Can do                                                                                                       |
|---------|--------------------------------------------------------------------------------------------------------------|
| Admin   | Everything a Manager can do, plus full CRUD on projects, project membership, and creating users with a role  |
| Manager | Create, edit, delete and assign tickets inside the projects they are a member of                             |
| User    | View tickets in their projects, comment, and change the status of tickets assigned to them                   |

- A Manager or User only sees the projects they are a member of. Admins see all projects.
- A ticket can only be assigned to a User who is a member of the ticket's project.
- Removing a member from a project unassigns their tickets in that project.
- "My Tickets": tickets created by the Manager/Admin, or tickets assigned to the User
  (Managers and Users only see tickets in projects they still belong to).
- Comments: anyone who can view a ticket can comment. An Admin can delete any comment,
  a Manager can delete comments on tickets they created, and a User only their own.
- Due dates must be real dates that are not in the past. When editing a ticket, an
  unchanged (already past) due date is accepted so other fields can still be updated.

Authorization is enforced by Policies (backend) and mirrored in the UI with `@can`,
so users are not shown actions they are not allowed to take.

## Notifications

| Event                           | Recipients                                                         |
|---------------------------------|--------------------------------------------------------------------|
| Ticket assigned                 | The assignee                                                       |
| New comment                     | The ticket's creator and assignee (including the comment's author) |
| Ticket overdue (daily command)  | The assignee, the project's Managers and all Admins                |

All three go through the same queued job, `SendTicketNotification`. Notifications are
stored in the `notifications` table and shown on the **Notifications** page.

## Architecture

- **Controllers** are thin: receive a request, call a service, return a response.
- **Form Requests** (`app/Http/Requests`) hold all validation, and call Policies from `authorize()`.
- **Policies** (`app/Policies`) hold all authorization logic. Read-only (GET) routes
  apply them through `can:` route middleware, and write requests apply them in the
  Form Request's `authorize()`. Both call the same Policy methods.
- **Services** (`app/Services`) hold the business logic and talk to the models.
- **Jobs / Notifications**: `SendTicketNotification` is dispatched on ticket assignment,
  on new comments, and by the daily overdue command.
- **Blade components** (`resources/views/components`):
  - `layout`: page shell, flash messages and validation errors
  - `nav`: top navigation, with role-aware links
  - `status-badge`: coloured ticket status
  - `status-form`: status dropdown for users allowed to change a ticket's status
  - `ticket-row`: one table row for a ticket, with an optional `actions` slot

## Tests

```bash
php artisan test
```

Feature tests cover project membership, ticket and comment permissions, assignment
rules, due-date validation, the notification jobs and the overdue command. Unit tests
cover `TicketPolicy`. Tests run on an in-memory SQLite database (see `phpunit.xml`).