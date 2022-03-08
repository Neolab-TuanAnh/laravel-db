<?php

namespace PandaDev\LangDb\Commands;

use Illuminate\Console\Command;
use PandaDev\LangDb\Models\Translation;
use Illuminate\Filesystem\Filesystem;
use PandaDev\LangDb\Output\File;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langdb:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate js from lang';

    /**
     * Files system instance
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->path = "./resources/js/lang.js";
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $langs = Translation::retrieve();
      $generatedLangs= $this->generate($langs);
      $this->makeDirectory($this->path);
      $this->files->put(base_path($this->path), $generatedLangs);

      $this->info('File generated!');
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname(base_path($path)))) {
            $this->files->makeDirectory(dirname(base_path($path)), 0755, true, true);
        }

        return $path;
    }

    private function generate(array $langs)
    {
        return (string) new File($langs);
    }
}
