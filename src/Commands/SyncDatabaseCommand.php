<?php

namespace PandaDev\LangDb\Commands;

use Illuminate\Console\Command;
use PandaDev\LangDb\Models\Translation;

class SyncDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'langdb:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data from lang file to database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $langs = Translation::getFileTranslations();
      foreach ($langs as $key => $lang) {
          if(Translation::where('key', $key)->count() === 0){
              $trans = $this->formatLang($lang);
              if(count($trans) > 0){
                $data = array_merge(['key' => $key], $trans);
                Translation::create($data);
              }
          }
      }

      echo "Sync to database success!";
    }

    protected function formatLang(array $lang){
      $trans = [];
      foreach ($lang as $i => $t) {
          if(!is_array($t)){
            $trans[$i] = ['value' => $t];
          }
      }
      return $trans;
    }
}
