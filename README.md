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

## Scheduled command

The command `tickets:notify-overdue` finds tickets whose due date has passed
(and that are not resolved or closed) and dispatches the notification job to the
assignee, the project's managers and all admins. It is scheduled to run daily
(`routes/console.php`).

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

| Role    | Can do                                                                                                   |
|---------|----------------------------------------------------------------------------------------------------------|
| Admin   | Everything a Manager can do, plus full CRUD on projects, project membership, and creating users with a role |
| Manager | Create, edit, delete and assign tickets inside the projects they are a member of                          |
| User    | View tickets in their projects, comment, and change the status of tickets assigned to them                |

- A Manager or User only sees the projects they are a member of. Admins see all projects.
- A ticket can only be assigned to a User who is a member of the ticket's project.
- "My Tickets": tickets created by the Manager/Admin, or tickets assigned to the User.
- Comments: anyone who can view a ticket can comment. An Admin can delete any comment,
  a Manager can delete comments on tickets they created, and a User only their own.

Authorization is enforced by Policies (backend) and mirrored in the UI with `@can`,
so users are not shown actions they are not allowed to take.

## Architecture

- **Controllers** are thin: receive a request, call a service, return a response.
- **Form Requests** (`app/Http/Requests`) hold all validation, and call Policies from `authorize()`.
- **Policies** (`app/Policies`) hold all authorization logic.
- **Services** (`app/Services`) hold the business logic and talk to the models.
- **Jobs / Notifications**: `SendTicketNotification` is dispatched on ticket assignment,
  on new comments, and by the daily overdue command. Notifications are stored in the
  `notifications` table and shown on the Notifications page.
- **Blade components** (`resources/views/components`): the `layout` and `status-badge`.

## Tests

```bash
php artisan test
```