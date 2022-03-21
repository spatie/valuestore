
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# Easily store some loose values

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/valuestore.svg?style=flat-square)](https://packagist.org/packages/spatie/valuestore)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
![Tests](https://github.com/spatie/valuestore/workflows/Tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/valuestore.svg?style=flat-square)](https://packagist.org/packages/spatie/valuestore)

This package makes it easy to store and retrieve some loose values. Stored values are saved as a json file.

It can be used like this:

```php
use Spatie\Valuestore\Valuestore;

$valuestore = Valuestore::make($pathToFile);

$valuestore->put('key', 'value');

$valuestore->get('key'); // Returns 'value'

$valuestore->has('key'); // Returns true

// Specify a default value for when the specified key does not exist
$valuestore->get('non existing key', 'default') // Returns 'default'

$valuestore->put('anotherKey', 'anotherValue');

// Put multiple items in one go
$valuestore->put(['ringo' => 'drums', 'paul' => 'bass']);

$valuestore->all(); // Returns an array with all items

$valuestore->forget('key'); // Removes the item

$valuestore->flush(); // Empty the entire valuestore

$valuestore->flushStartingWith('somekey'); // remove all items whose keys start with "somekey"

$valuestore->increment('number'); // $valuestore->get('number') will return 1 
$valuestore->increment('number'); // $valuestore->get('number') will return 2
$valuestore->increment('number', 3); // $valuestore->get('number') will return 5

// Valuestore implements ArrayAccess
$valuestore['key'] = 'value';
$valuestore['key']; // Returns 'value'
isset($valuestore['key']); // Return true
unset($valuestore['key']); // Equivalent to removing the value

// Valuestore implements Countable
count($valuestore); // Returns 0
$valuestore->put('key', 'value');
count($valuestore); // Returns 1
```

Read the [usage](#usage) section of this readme to learn about the other methods.

In [this post on Laravel News](https://laravel-news.com/global-application-settings), [Tim MacDonald](https://twitter.com/timacdonald87) shares how you can use this package to power a `settings` function.

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/valuestore.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/valuestore)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

``` bash
composer require spatie/valuestore
```

## Usage

To create a Valuestore use the `make` method.

```php
$valuestore = Valuestore::make($pathToFile);
```

You can also pass some values as a second argument. These will be added to the valuestore using the `put` method.

```php
$valuestore = Valuestore::make($pathToFile, ['key' => 'value']);
```

All values will be saved as json in the given file.

When there are no values stored, the file will be deleted.

You can call the following methods on the `Valuestore`

### put
```php
/**
 * Put a value in the store.
 *
 * @param string|array $name
 * @param string|int|null $value
 * 
 * @return $this
 */
public function put($name, $value = null)
```

### get

```php
/**
 * Get a value from the store.
 *
 * @param string $name
 *
 * @return null|string
 */
public function get(string $name)
```

### has

```php
/*
 * Determine if the store has a value for the given name.
 */
public function has(string $name) : bool
```

### all
```php
/**
 * Get all values from the store.
 *
 * @return array
 */
public function all() : array
```

### allStartingWith
```php
/**
 * Get all values from the store which keys start with the given string.
 *
 * @param string $startingWith
 *
 * @return array
*/
public function allStartingWith(string $startingWith = '') : array
```

### forget
```php
/**
 * Forget a value from the store.
 *
 * @param string $key
 *
 * @return $this
 */
public function forget(string $key)
```

### flush
```php
/**
 * Flush all values from the store.
 *
 * @return $this
 */
 public function flush()
```

### flushStartingWith
```php
/**
 * Flush all values from the store which keys start with the specified value.
 *
 * @param string $startingWith
 *
 * @return $this
 */
 public function flushStartingWith(string $startingWith)
```

### pull
```php
/**
 * Get and forget a value from the store.
 *
 * @param string $name 
 *
 * @return null|string
 */
public function pull(string $name)
```

### increment
```php
/**
 * Increment a value from the store.
 *
 * @param string $name
 * @param int $by
 *
 * @return int|null|string
 */
 public function increment(string $name, int $by = 1)
```

### decrement
```php
/**
 * Decrement a value from the store.
 *
 * @param string $name
 * @param int $by
 *
 * @return int|null|string
 */
 public function decrement(string $name, int $by = 1)
```

## push
```php
/**
 * Push a new value into an array.
 *
 * @param string $name
 * @param $pushValue
 *
 * @return $this
 */
public function push(string $name, $pushValue)
```

## prepend
```php
/**
 * Prepend a new value into an array.
 *
 * @param string $name
 * @param $prependValue
 *
 * @return $this
 */
public function prepend(string $name, $prependValue)
```

## count
```php
/**
 * Count elements.
 *
 * @return int
 */
public function count()
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [Jolita Grazyte](https://github.com/JolitaGrazyte)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
