<?php

namespace Database\Seeders;

use App\Models\User;
use RuntimeException;
use JsonStreamingParser\Parser;
use JsonStreamingParser\Listener\InMemoryListener;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        $filePath = database_path('seeders/cities.list.json.gz');
        
        if (!\Illuminate\Support\Facades\File::exists($filePath)) {
            throw new RuntimeException("Cities data file not found at: {$filePath}");
        }

        $stream = gzopen($filePath, 'r');
        $listener = new InMemoryListener();
        $parser = new Parser($stream, $listener);
        $parser->parse();
        $cities = $listener->getJson();
        
        $progressBar = $this->command->getOutput()->createProgressBar(count($cities));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %memory:6s%');
        $progressBar->start();

        $chunk = [];
        
        foreach ($cities as $city) {
            $chunk[] = [
                'original_id' => $city['id'],
                'name' => $city['name'],
                'state' => $city['state'] ?? null,
                'country' => $city['country'],
                'lat' => $city['coord']['lat'],
                'lon' => $city['coord']['lon'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if (count($chunk) >= 4500) {
                $this->processChunk($chunk, $progressBar);
                $chunk = [];
            }
        }
        
        // Process remaining records
        if (!empty($chunk)) {
            $this->processChunk($chunk, $progressBar);
        }
        
        fclose($stream);
        
        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info('Cities seeded successfully!');
    }

    protected function processChunk(array &$chunk, $progressBar): void
    {
        try {
            \DB::transaction(function () use ($chunk, $progressBar) {
                $existingIds = \DB::table('cities')
                    ->whereIn('original_id', array_column($chunk, 'original_id'))
                    ->pluck('original_id')
                    ->all();
                
                $newRecords = array_filter($chunk, fn($r) => !in_array($r['original_id'], $existingIds));
                
                if (!empty($newRecords)) {
                    foreach (array_chunk($newRecords, 1000) as $batch) {
                        \DB::table('cities')->insert($batch);
                    }
                }
                
                $progressBar->advance(count($newRecords));
            });
        } catch (\Exception $e) {
            $this->command->error("Error: ".$e->getMessage());
        }
    }
}
