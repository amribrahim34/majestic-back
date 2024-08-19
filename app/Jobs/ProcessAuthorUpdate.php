<?php

namespace App\Jobs;

use App\Imports\AuthorUpdateImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Author;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProcessAuthorUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $jobId;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->jobId = uniqid('author_update_', true);
    }

    public function handle()
    {
        Excel::import(new AuthorUpdateImport($this->jobId), $this->filePath);
        Storage::delete($this->filePath);
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public static function getProgress($jobId)
    {
        return Cache::get("author_update_progress_{$jobId}", 0);
    }
}
