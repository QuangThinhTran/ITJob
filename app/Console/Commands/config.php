<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class config extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Config env';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('view:clear');
        $this->call('view:cache');
        $this->call('route:cache');
        $this->call('route:clear');
        $this->call('event:cache');
        $this->call('optimize:clear');
        $this->info('Successfully reload caches.');
    }
}
