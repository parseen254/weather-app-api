<?php

namespace Database\Seeders;

use App\Models\User;
use RuntimeException;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/cities.list.json.gz');
        
        if (!\Illuminate\Support\Facades\File::exists($filePath)) {
            throw new RuntimeException("Cities data file not found at: {$filePath}");
        }

        $data = json_decode(implode('', gzfile($filePath)), true, 512, JSON_THROW_ON_ERROR);
        
        $this->command->info("Seeding ".count($data)." cities...");
        $progressBar = $this->command->getOutput()->createProgressBar();
        $progressBar->setMaxSteps(count($data));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message% Mem: %memory:6s%');
        $progressBar->start();

        collect($data)->chunk(1000)->each(function ($chunk) use ($progressBar) {
            try {
                \DB::transaction(function () use ($chunk) {
                    $records = $chunk->map(function ($city) {
                        return [
                            'original_id' => $city['id'],
                            'name' => $city['name'],
                            'state' => $city['state'] ?? '',
                            'country' => $city['country'],
                            'lat' => $city['coord']['lat'],
                            'lon' => $city['coord']['lon'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    })->toArray();
                    
                    // Filter out duplicates
                    $existingIds = \DB::table('cities')
                        ->whereIn('original_id', array_column($records, 'original_id'))
                        ->pluck('original_id')
                        ->all();
                    
                    $newRecords = array_filter($records, fn($r) => !in_array($r['original_id'], $existingIds));
                    
                    if (!empty($newRecords)) {
                        \DB::table('cities')->insert($newRecords);
                    }
                    
                });
                $progressBar->advance($chunk->count());
            } catch (\Exception $e) {
                $this->command->error("Error: ".$e->getMessage());
            }
        });

        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info('Cities seeded successfully!');
    }
}
