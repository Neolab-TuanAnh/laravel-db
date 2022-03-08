<?php
namespace PandaDev\LangDb\Output;

use Stringable;

class File implements Stringable
{
    protected $translations;

    public function __construct(array $translations)
    {
        $this->translations = $translations;
    }

    public function __toString(): string
    {
        $langs = json_encode($this->translations);
        $defaultLocate = config('app.locale');

        return <<<JAVASCRIPT
        const TRANS = {$langs};

        function setLangLocale(locale = "{$defaultLocate}"){
          if(typeof window !== 'undefined'){
            window.langLocate = locale;
          }
        }

        export {
          setLangLocale
        }
        
        /**
        * Trans text with key
        * 
        * @param {String} key
        * @param {Object} options
        * @return {String}
        */
        export default function trans(key, options = {}){
          if(!key || !TRANS.hasOwnProperty(key)) return null;

          const locate = window.langLocate || "{$defaultLocate}";
          let lang = TRANS[key][locate];

          if(!lang) return null;

          /**
           * Assign attributes to trans text markdown
           */
          Object.keys(options).forEach(function(i){
            lang = lang.replace(":" + i, options[i]);
          });

          return lang;
        }
        JAVASCRIPT;
    }
}