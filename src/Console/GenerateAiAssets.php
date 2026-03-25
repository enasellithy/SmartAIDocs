<?php

namespace SmartAIDocs\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SmartAIDocs\Services\AiService;

class GenerateAiAssets extends Command
{
    protected $signature = 'ai:generate {path} {--docs} {--test}';
    protected $description = 'Process all files in a directory (like Controllers) and generate AI assets';

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
            $this->info("✅ Bulk processing finished!");
        } else {
            $this->processFile($path, File::get($fullPath), $ai);
        }
    }

    protected function processFile($path, $code, $ai)
    {
        if ($this->option('docs')) {
            $prompt = "Analyze the following Laravel code and generate a Markdown documentation. " .
                "For EVERY function/method, provide: 1. Purpose, 2. Parameters, 3. Return Value. \n\n Code: \n" . $code;

            $doc = $ai->ask($prompt);
            if ($doc) $this->saveResult($path, $doc, 'md');
        }

        if ($this->option('test')) {
            $prompt = "Generate a comprehensive PHPUnit test. Ensure you cover EVERY public function in this class with at least one test case. \n\n Code: \n" . $code;

            $test = $ai->ask($prompt);
            if ($test) $this->saveResult($path, $test, 'php');
        }
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
