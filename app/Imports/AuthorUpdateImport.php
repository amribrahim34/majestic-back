<?php

namespace App\Imports;

use App\Models\Author;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthorUpdateImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    protected $jobId;

    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    public function collection(Collection $rows)
    {
        $validatedRows = $this->validateRows($rows);

        if (!empty($validatedRows)) {
            Author::upsert($validatedRows, ['id'], ['name', 'biography', 'birth_date', 'death_date', 'country']);
        }

        $this->updateProgress(count($validatedRows));
    }

    protected function validateRows(Collection $rows)
    {
        return $rows->filter(function ($row) {
            $validator = Validator::make($row->toArray(), [
                'id' => 'required|integer|exists:authors,id',
                'name' => 'required|string|max:255',
                'biography' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'death_date' => 'nullable|date|after:birth_date',
                'country' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                // Log the validation errors or handle them as needed
                Log::error('Validation failed for row: ' . json_encode($row) . ' Errors: ' . json_encode($validator->errors()));
                return false;
            }

            return true;
        })->map(function ($row) {
            return $row->toArray();
        })->toArray();
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function updateProgress($count)
    {
        $progress = Cache::get("author_update_progress_{$this->jobId}", 0);
        $newProgress = min(100, $progress + ($count * 0.1)); // Increment by 0.1% per row, max 100%
        Cache::put("author_update_progress_{$this->jobId}", $newProgress, now()->addHours(2));
    }
}
