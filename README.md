# Blackhole
A simple ticketing system for Mr. H.

# Why?
Because I can, also memes.

# Buy why?
So that people can take it apart and figure out how it works. Something simple and easy to use and work with.
Caching not included, some assembly required. 

# Development
This is a simple PHP-based ticket system using MySQL. It contains inconsistencies, bad practices and questionable code. Feel free to build/fix the platform. You can do so by forking it and then creating a pull request after any changes. Feel free to take it apart and try to figure out how it works.

# Requirements
* Apache (nginx is preferred)
* PHP 5.6.x or greater
* MySQL 5.5.x or greater (MariaDB preferable)
* Cup of coffee

# Installation
1. Setup LAMP stack
2. Clone this git repo
3. Import SQL dump
4. Configure database config in `/library/config/app.php`
5. Profit?

# General Features
* Obivous tickets and replying
  * Closing, merging and deleting tickets
* Ticket and message attachment support
* Simple searching of tickets
* Basic user authentication
  * Administrative and regular users
  * General account management
* Various easter eggs and memes

# Todo
* Database-based sessions
* Notifications (push / email?)
* Add proper validation and more security stuff (CSRF, rate-limiting, etc)
* Make it look pretty, use tabs not spaces (thanks cloud9) and add comments
