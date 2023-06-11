# Laravel Multenv

Manage multiple .env files in Laravel with automatic encryption/decryption to embed and share your .env files in your repository.

## Installation

You can install the package via composer:

```bash
composer require ibrostudio/laravel-multenv
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="multenv-config"
```

This is the contents of the published config file:

```php
return [

    'include' => [
        '.env.primary' => ['encrypt' => true],  // Base env file, will be encrypted to be included in repo
        '.env.branch' => ['encrypt' => true],   // optional, content override base values, will be encrypted to be included in repo
        '.env.custom' => ['encrypt' => false],  // optional, content override previous values, exclude this with gitignore
    ],
];
```
You define which .env files you want to manage:
- a primary .env file, reflecting your production environment with all variables
- then, you can add one (or more) entries to override some variables (for local use or branch differences)

## Usage

At the root of your project, create all files defined in the config and populate them with variables.

Add them to your .gitignore:

```
.env.primary
.env.branch
.env.custom
```
or
```
.env*
```


Then run:
```bash
php artisan multenv:merge
```
It generates the final .env file, merging variables from all configured .env files.

Each file overrides variables from the previous one.

## Encryption

You can encrypt some .env files to embed them securely in your repo.

**Generate encryption key**

```bash
php artisan multenv:key
```

Add it to your .gitignore:

```
.multenv
```

**Don't commit your .multenv key in your repo, it is a security risk.**

If you work with people on the repo, share manually the key, IT HAS TO BE SAME for everyone.

**Configure the files to encrypt**

In config/multenv.php, define each file to encrypt with the setting to true

```php
'.env.primary' => ['encrypt' => true]
```

**Encrypt**

```bash
php artisan multenv:encrypt
```

If you use **.env** in your .gitignore, add after:

```
.env*
!.env.*.encrypted
```

**Decrypt**

```bash
php artisan multenv:decrypt
```

## Automatic encryption/decryption

You can define some git hooks to automate the processes:

**1. Merge, encrypt and commit**

Create (or modify) a file called ***pre-push*** in .git/hooks and add in it:

```bash
#!/bin/sh

echo "---- PRE PUSH ----"
php artisan multenv:encrypt
git add .env.*.encrypted
git commit -m 'Auto embed encrypted env files'
echo "--- PRE PUSH END ---"
```

Make it executable:

```bash
chmod +x pre-push
```

**2. Decrypt and merge**

Create (or modify) a file called ***post-merge*** in .git/hooks and add in it:

```bash
#!/bin/sh

echo "---- POST MERGE ----"
php artisan multenv:decrypt
php artisan multenv:merge
echo "--- POST MERGE END ---"
```

Make it executable:

```bash
chmod +x post-merge
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
