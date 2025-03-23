<?php

use App\Jobs\Api\V1\ProcessScheduledJobs;

Schedule::job(new ProcessScheduledJobs())->everyMinute();

