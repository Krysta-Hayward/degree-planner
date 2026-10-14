# Degree Planner
[![CI](https://github.com/Krysta-Hayward/degree-planner/actions/workflows/ci.yaml/badge.svg?branch=main)](https://github.com/Krysta-Hayward/degree-planner/actions/workflows/ci.yaml?query=branch%3Amain)

A web application designed to help college students plan and track their degree progress.

## Problem

Many students, especially freshmen, have difficulty figuring out which courses they need to take and when they should take them. Missing prerequisites or taking courses in the wrong order can delay graduation and create additional costs and stress.

The Degree Planner helps students understand their degree requirements, plan future courses, and keep track of their progress.

## Users

### Students

Students can:

* Select and plan their degree program
* Add completed courses
* Plan courses for future terms
* See prerequisites and course availability
* Track their degree progress

### Advisors

Advisors can:

* View student degree plans
* Help students plan their courses
* Comment on student plans
* Review and approve submitted plans

### Administrators

Administrators can:

* Add and edit courses
* Manage degree programs and requirements
* Assign students to advisors

## Features

* Student, advisor, and administrator accounts
* Role-based access
* Course and degree catalog
* Course prerequisites
* Term-by-term course planning
* Course availability
* Schedule conflict warnings
* Credit tracking
* Degree progress tracking
* Advisor plan review and comments

## Project Scope

The project will be developed across five main areas:

1. **Accounts and Access**
2. **Course and Program Catalog**
3. **Term Planning**
4. **Degree Progress Tracking**
5. **Advisor Review**

## Out of Scope

The following features are not planned for this project:

* Google/Facebook login
* Two-factor authentication
* Password recovery
* Direct messaging
* Push notifications
* Mobile application
* Dark mode
* Multiple languages
* Advanced search filters

## Technology

* PHP
* Laravel
* MongoDB

## Project Status

This project is currently under development as a team school project.

## Definition of Done
* Acceptance criteria met
* Tests written and passing (php artisan test, vendor/bin/pint --test)
* Pull request reviewed and approved by at least one teammate
* CI passing
* Merged to main and deployed
* Any documentation the change requires is updated

## How To Run
* Clone the GitHub repo (SSH) ```git clone git@github.com:Krysta-Hayward/degree-planner.git```
* cd into the degree planner ```cd degree-planner```
* Copy environment file ```cp .env.example .env```
* Copy testing environment file ```cp .env.testing.example .env.testing```
* Install dependencies. Make sure you have PHP, Composer, and Docker Desktop installed locally, then run ```composer install```
* Generate application key ```php artisan key:generate```
* Generate testing key ```php artisan key:generate --env=testing```
* Create the SQLite database ```New-Item database/database.sqlite -ItemType File```
* Run database migrations ```php artisan migrate```
* Start Docker ```docker compose up -d --build```
* Visit the application ```http://localhost```
* Visit the health check page ```http://localhost/health```
* Run tests ```php artisan test```
* Check Pint ```vendor/bin/pint --test```

## Easy copy and paste commands
```
git clone git@github.com:Krysta-Hayward/degree-planner.git
cd degree-planner
cp .env.example .env
cp .env.testing.example .env.testing
composer install
php artisan key:generate
php artisan key:generate --env=testing
New-Item database/database.sqlite -ItemType File
php artisan migrate
docker compose up -d --build
php artisan test
vendor/bin/pint --test
```

## Stop Docker
* ```docker compose down```
