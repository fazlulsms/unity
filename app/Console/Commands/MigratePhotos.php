<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigratePhotos extends Command
{
    protected $signature   = 'photos:migrate {--dest= : Absolute path to the uploads destination directory}';
    protected $description = 'Copy old photos from storage/app/public/ into the public uploads directory';

    public function handle(): int
    {
        $dest = $this->option('dest')
            ?? public_path('uploads');

        $this->info("Destination: {$dest}");
        $this->newLine();

        $src  = storage_path('app/public');
        $dirs = ['applications', 'profiles', 'members'];

        $copied  = 0;
        $existed = 0;
        $failed  = 0;

        foreach ($dirs as $dir) {
            $srcDir = $src  . DIRECTORY_SEPARATOR . $dir;
            $dstDir = $dest . DIRECTORY_SEPARATOR . $dir;

            if (!is_dir($srcDir)) {
                $this->line("  <comment>skip</comment>   {$dir}/ — source not found");
                continue;
            }

            if (!is_dir($dstDir)) {
                mkdir($dstDir, 0755, true);
            }

            $files = array_filter(glob($srcDir . DIRECTORY_SEPARATOR . '*'), 'is_file');
            foreach ($files as $file) {
                $name   = basename($file);
                $target = $dstDir . DIRECTORY_SEPARATOR . $name;

                if (file_exists($target)) {
                    $this->line("  <comment>exists</comment>  {$dir}/{$name}");
                    $existed++;
                    continue;
                }

                if (@copy($file, $target)) {
                    $this->line("  <info>copied</info>  {$dir}/{$name}");
                    $copied++;
                } else {
                    $this->line("  <error>failed</error>  {$dir}/{$name}");
                    $failed++;
                }
            }
        }

        $this->newLine();
        $this->info("Done — copied: {$copied}, already existed: {$existed}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
