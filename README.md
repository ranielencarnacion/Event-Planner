# Event Planner

Our group project - a simple web app for planning and managing events. Built using PHP and MySQL with an easy-to-use calendar interface where you can add, edit, and organize your events.

## What it does

- Login and register new accounts
- Add, edit, delete and move events around
- Nice calendar view (thanks to DayPilot library)
- Get notifications for upcoming events
- Change your password when needed
- Color code your events to stay organized

## Tech we used

- PHP for the backend stuff
- MySQL database to store everything
- Regular HTML, CSS, and JavaScript for the frontend
- DayPilot library for the calendar
- Runs on Apache (we used XAMPP for development)

## Project Structure

```
sys/
├── alarm/                  # Audio files for notifications
│   └── alarm.wav
├── icons/                  # SVG icons
│   └── daypilot.svg
├── img/                    # Images and logos
│   ├── logo.png
│   └── note.png
├── js/                     # JavaScript libraries
│   └── daypilot/          # DayPilot calendar library
├── index.php              # Main dashboard/calendar view
├── login.php              # User login page
├── register.php           # User registration page
├── logout.php             # Logout functionality
├── changepassword.php     # Password change interface
├── calendar_*.php         # Calendar CRUD operations
├── notifications_fetch.php # Notification handling
├── _db.php                # Database connection (SQLite)
├── _db_mysql.php          # Database connection (MySQL)
├── 127_0_0_1.sql          # Database schema and sample data
└── .gitignore             # Git ignore rules
```

## How to run it

### What you need
- XAMPP (or similar local server)
- PHP 8.0+
- MySQL
- Any web browser

### Setup steps

1. **Get the files**
   - Download or clone this project

2. **Database setup**
   - Start XAMPP and turn on MySQL
   - Go to phpMyAdmin and import the `127_0_0_1.sql` file
   - This creates the database and tables we need

3. **Check database settings**
   - Look at `_db_mysql.php` - should work with default XAMPP settings
   - If your MySQL has a password, update it there

4. **Put files in the right place**
   - Copy everything to your `htdocs` folder (like `htdocs/sys/`)

5. **Try it out**
   - Go to `http://localhost/sys/` in your browser
   - Create an account and start adding events!

## How to use it

1. Make an account (or login if you already have one)
2. You'll see the main calendar page
3. Click on any date to add a new event
4. Click on existing events to edit or delete them
5. Drag events around to move them to different dates
6. Set up notifications so you don't forget important stuff

## Database info

We use a `calendar` database with these tables:
- `users` - stores user accounts
- `events` - all the event details
- `notifications` - notification settings

## Group Members

Made by our group:

- [Raniel Encarnacion - Leader]
- [Bernard Rivera]
- [Ivan Lumbang]
- [Ciream Gil Bondoc]

## Notes

- We included both SQLite and MySQL connection files (we ended up using MySQL)
- The calendar uses DayPilot which makes it look pretty nice
- Works on phones and computers
- Uses PHP sessions to keep track of who's logged in

## Want to improve it?

Feel free to fork this and make it better! Just make sure to test your changes before submitting.

## Questions?

If something's not working or you have questions, just reach out to any of us.

---

*Project completed December 2024*
