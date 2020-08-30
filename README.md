# Test-Composer-Plugin

The idea is to have a plugin that checks whether there is a PHAR-file of 
a dev-tool available and then installs that instead of the source-files of the tool

## This is currently a PoC

## Test

Create a new repository and add the following to the `composer.json`:

```json
{
    "repositories": [{
        "type": "vcs",
        "url": "https://github.com/heiglandreas/testcomposerplugin"
    }]
}
```

Then you should be able to run `composer require composer/composer`. The result should be that
your `composer.json` contains an entry for `composer/composer` but it will currently not be installed.

## Next steps

* Check for the source-path of the package
* see whether there is a build-artifact available with the given version constraint
* download it
* Check the signature
