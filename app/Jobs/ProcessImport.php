<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ProcessImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $importClass;
    protected $filePath;
    protected $jobId;

    public function __construct(string $importClass, string $filePath)
    {
        $this->importClass = $importClass;
        $this->filePath = $filePath;
        $this->jobId = uniqid('import_', true);
    }

    public function handle()
    {
        Excel::import(new $this->importClass, $this->filePath);
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public function updateProgress($progress)
    {
        Cache::put("import_progress_{$this->jobId}", $progress, now()->addHours(2));
    }

    public static function getProgress($jobId)
    {
        return Cache::get("import_progress_{$jobId}", 0);
    }
}
