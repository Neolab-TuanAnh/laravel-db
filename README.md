# Lang DB – Use laravel translation with database and in Javascript

```php
// Sync data from all lang files to database

# php artisan langdb:sync
```

```php
// Generate js file for trans

# php artisan langdb:generate
```

## Installation
You can optionally create a webpack alias to make importing translation helper function:

```js
// webpack.mix.js

// Mix v6
const path = require('path');

mix.alias({
    lang: path.resolve('resources/js/lang.js'),
});

// Mix v5
const path = require('path');

mix.webpackConfig({
    resolve: {
        alias: {
            lang: path.resolve('resources/js/lang.js'),
        },
    },
});
```

## Basic Usage

```js
// app.js
import trans from 'lang';
const text = trans('validation.boolean', { attribute: 'User' });

// Laravel lang: The :attribute field must be true or false.
// Ouput: The User field must be true or false.
```