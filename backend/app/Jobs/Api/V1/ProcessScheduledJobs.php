<?php

namespace App\Jobs\Api\V1;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessScheduledJobs implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Post::where('status', 'SCHEDULED')
            ->where('scheduled_at', '<=', Carbon::now())
            ->update([
                'status' => 'PUBLISHED',
                'scheduled_at' => null
            ]);
    }
}
