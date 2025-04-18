# Contributing to the project

First of all, thank you for your interest in contributing to the project! We appreciate your help and support.

This document will guide you through the process of contributing to the project, including how to set up your development environment, run tests, and submit your changes.

## 🚀 Getting Started

You need to have PHP 8.4, Composer and Node.js installed on your machine to run the website locally.

You can install PHP and Composer using [php.new](https://php.new), or alternatively for a more robust solution, you can use [Laravel Herd](https://herd.laravel.com/).

## 👨‍💻 Running locally

```bash
# Clone the repository
git clone git@github.com:playsaurus-inc/play-bench.git
cd play-bench

# Install dependencies
composer install
npm install

# Run migrations and seed the database with fake data
php artisan migrate --seed

# Start the local development server (Serve + Queues + Logs + Assets)
composer run dev
```

Then open [http://localhost:8000](http://localhost:8000) in your browser.

## 💅 Code style

The code style is based on the [Laravel coding style](https://laravel.com/docs/10.x/contributions#coding-style) and [PSR-12](https://www.php-fig.org/psr/psr-12/).

The project uses [Laravel Pint](https://laravel.com/docs/pint) for code formatting. You can run it with the following command:

```bash
composer run pint     # Alias for `./vendor/bin/pint`
```

## 🌐 Deploying

> [!NOTE] > **Only for maintainers:** This section is only for maintainers of the project. If you are just a contributor, you don't need to worry about this section. The deployment process will be done by the maintainers of the project.

The website is deployed using [Laravel Forge](https://forge.laravel.com/) and GitHub Actions.

Simply create a new release in GitHub and the website will be automatically deployed to the server.

> [!NOTE] > **How it works:** When you create a new Github release, a GitHub Action will merge the `main` branch into the `production` branch and Forge will deploy the changes. The deployment is handled by Laravel Forge using the `production` branch.
