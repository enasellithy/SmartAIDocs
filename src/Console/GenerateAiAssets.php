<?php

namespace SmartAIDocs\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SmartAIDocs\Services\AiService;

class GenerateAiAssets extends Command
{
    protected $signature = 'ai:generate {path} {--docs} {--test}';
    protected $description = 'Process all files in a directory and generate AI assets';

    public function handle(AiService $ai)
    {
        $path = $this->argument('path');
        $fullPath = base_path($path);

        if (!File::exists($fullPath)) {
            $this->error("Path not found: $path");
            return;
        }

        if (File::isDirectory($fullPath)) {
            $files = File::allFiles($fullPath);
            $this->info("🚀 Found " . count($files) . " files. Starting Bulk AI Processing...");

            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    $this->warn("Processing: " . $file->getRelativePathname());
                    $this->processFile($file->getRelativePathname(), $file->getContents(), $ai);
                }
            }
            
            if ($this->option('docs')) {
                $this->generateSummary();
            }
            
            $this->info("✅ Bulk processing finished!");
        } else {
            $this->processFile($path, File::get($fullPath), $ai);
        }
    }

    protected function processFile($path, $code, $ai)
    {
        if ($this->option('docs')) {
            $prompt = "Act as a Senior Technical Writer. Convert the following Laravel code into a high-quality Wiki Documentation page.
                1. Start with a Breadcrumb navigation (e.g., Docs > Module > Class).
                2. Use H2 for the Class Name and a brief overview of its responsibility.
                3. Group functions into logical sections (e.g., 'Data Access', 'Business Logic') instead of just listing them.
                4. For each section, use narrative text to explain HOW the methods work together.
                5. Use Callouts (e.g., > [!NOTE]) for important details.
                6. DO NOT use only tables; use descriptive paragraphs.
                
                File Path: {$path}
                Code: \n" . $code;

            $doc = $ai->ask($prompt);
            if ($doc) $this->saveResult($path, $doc, 'md');
        }

        if ($this->option('test')) {
            $prompt = "Generate a comprehensive PHPUnit test. Ensure you cover EVERY public function in this class with at least one test case. \n\n Code: \n" . $code;

            $test = $ai->ask($prompt, null, 'tests');
            if ($test) $this->saveResult($path, $test, 'php');
        }
    }

    protected function generateSummary()
    {
        $folder = config('smart-ai-docs.output_paths.docs', 'docs/ai-generated');
        $fullFolderPath = base_path($folder);
        
        if (!File::isDirectory($fullFolderPath)) return;

        $files = File::files($fullFolderPath);
        $summary = "# Table of Contents\n\n";
        $summary .= "* [Introduction](README.md)\n";
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'md' && $file->getFilename() !== 'SUMMARY.md') {
                $name = str_replace('.md', '', $file->getFilename());
                $summary .= "* [{$name}]({$file->getFilename()})\n";
            }
        }

        File::put($fullFolderPath . '/SUMMARY.md', $summary);
        $this->info("   - Generated: SUMMARY.md");
    }

    protected function saveResult($originalPath, $content, $ext)
    {
        $filename = basename($originalPath, '.php');
        $configKey = ($ext === 'md') ? 'smart-ai-docs.output_paths.docs' : 'smart-ai-docs.output_paths.tests';
        $folder = config($configKey, ($ext === 'md' ? 'docs/ai-generated' : 'tests/Unit/AI'));

        if (!File::isDirectory(base_path($folder))) {
            File::makeDirectory(base_path($folder), 0755, true);
        }

        $newPath = base_path($folder . '/' . $filename . ($ext === 'md' ? '.md' : 'Test.php'));
        File::put($newPath, $content);
        $this->line("   - Generated: " . basename($newPath));
    }
}