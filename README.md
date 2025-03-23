# Post Scheduler – Social Media Post Management


# 📋Contents
- [Features](#features)
- [Tools & Frameworks](#tools--frameworks)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
  - [Backend Installation](#backend-installation)
  - [Frontend Installation](#frontend-installation)
- [Contact](#contact)

# Features
- Schedule posts for future publication
- Automatic Posting
- Robust Validation
- SPA Experience
- Responsive design
- Friendly-design

# Tools & Frameworks
- Laravel 12
- Nuxt.js 3.16
- TailwindCSS 4.0

# Prerequisites
- PHP 8.2 or higher
- Composer
- NPM
- PostgreSQL (or any SQL DB)


# Installation
This project consists of 2 sub-projects; API Backend using Laravel, and Frontend client using Nuxt.js. We will install both projects



> **Note:** The following installation is directed for testing environment. This setup is not production-ready as this is not the purpose of this project. Further optimizations will be needed to convert it to a production service as using Docker, Octane and Supervisord.


### Backend Installation

```
 git clone https://github.com/ahmed-fawzy99/mock-post-scheduler.git
 cd mock-post-scheduler/backend
 composer install
 php artisan key:generate
 cp .env.example .env 
 ```
Then open `.env` file do the following:
- Fill in your database credentials.
- Make sure `QUEUE_CONNECTION=database` is set for queues.
- If you need to receive emails for testing, please add your SMTP settings or use [mailpit](https://mailpit.axllent.org/).
- Nuxt will run on port 3000 by default. If you are already using this port, please update the `FRONTEND_URL` field.


Make a symbolic link to the storage directory:
```bash
 php artisan storage:link
```

To generate basic data needed to start and test the system, we need to run the migrations and starter seeder:
```bash
 php artisan migrate --seed
```

You will need to run the queue for task scheduling and queues, and leave it running:
```bash
 php artisan queue:work --tries=3 
```
> In production, this command must be used within a supervisord for resiliance.

Create a cron job to process schedules posts. The following process is for **UNIX-like** operating systems. If you are on Windows, follow [these instructions](https://gist.github.com/Splode/94bfa9071625e38f7fd76ae210520d94).
```bash
# Assuming cron is installed. Follow you distro instructions to install it otherwise.
crontab -e

# then, inside the cron file add thisL
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
Run the development server
```bash
php artisan serve
```
### Frontend Installation

For Client-side Dependencies:
```
 cd ../frontend
 npm install
```
Laravel runs on port 8000 by default. If you have changed this, update `backendBase` and `backendApi` in `nuxt.config.ts

Finally, Run the frontend server:
```
 npm run dev
```

# Contact
You can reach me using any of the following media:
- [Linkedin](http://linkedin.com/in/ahmeddeghady)
- [ahmaddeghady99@gmail.com](mailto:ahmaddeghady99@gmail.com)
- [Website](http://ahmaddeghady.online/)