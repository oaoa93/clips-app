<?php

namespace App\Jobs;

use App\Models\Clip;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessClipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Clip $clip) {}

    public function handle(): void
    {
        Log::info('Clip processed simulation completed.', [
            'clip_id' => $this->clip->id,
            'title' => $this->clip->title,
        ]);
    }
}
