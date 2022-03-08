<?php

namespace PandaDev\LangDb\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;
use PandaDev\LangDb\LangDb;
use PandaDev\LangDb\TranslationLoader;
use PandaDev\LangDb\Commands\SyncDatabaseCommand;
use PandaDev\LangDb\Commands\GenerateCommand;
use Astrotomic\Translatable\Locales;
use Illuminate\Translation\TranslationServiceProvider;

class LangDbServiceProvider extends TranslationServiceProvider
{
    /**
     * BsForm any application services.
     *
     * @return void
     */
    public function boot()
    {
        // publish config
        $this->publishes([
            $this->srcPath('config/lang-db.php') => config_path('lang-db.php'),
        ]);

        // load migration
        $this->loadMigrationsFrom($this->srcPath('database/migrations'));

        // load service
        $this->app->singleton('lang_db', function ($app) {
            return new LangDb();
        });

        if (! config('lang-db.active')) {
          return;
        }
        
        if ($this->app->runningInConsole()) {
          $this->commands([
              SyncDatabaseCommand::class,
              GenerateCommand::class
          ]);
        }

        // setup service
        $this->setupTranslatable();
    }

    private function setupTranslatable()
    {
        $this->app['config']->set('translatable.use_fallback', true);
        $this->app['config']->set('translatable.fallback_locale', config('app.fallback_locale'));
        $this->app['config']->set('translatable.locales', config('lang-db.supported_locale_keys'));

        // Re-register translatable locales helper after overriding config.
        $this->app->singleton('translatable.locales', Locales::class);
        $this->app->singleton(Locales::class);
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        // The translation loader must be registered first.
        // $this->registerLoader();
        // $this->registerTranslator();

        // merge config
        $this->mergeConfigFrom($this->srcPath('config/lang-db.php'), 'lang-db');
    }

    private function srcPath($path)
    {
        return __DIR__ . '/../' . $path;
    }

    /**
     * Register the translation line loader. This method registers a
     * `TranslationLoaderManager` instead of a simple `FileLoader` as the
     * applications `translation.loader` instance.
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new TranslationLoader($app['files'], $app['path.lang']);
        });
    }

    protected function registerTranslator()
    {
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }
}
