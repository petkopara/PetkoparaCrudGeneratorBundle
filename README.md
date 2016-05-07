# CrudGeneratorBundle
Symfony3 CRUD generator bundle with pagination, filter, bulk delete and Twitter bootstrap 3.3.6 features.
It's highly customizable and flexible. 

[![Build Status](https://travis-ci.org/petkopara/TritonCrudBundle.svg?branch=master)](https://travis-ci.org/petkopara/TritonCrudBundle)


## Screenshot

![Screenshot](https://raw.github.com/Triton/CrudGeneratorBundle/master/screenshot.png "Screenshot")

## Installation
This bundle is compatible with Symfony 2.8/3.0 or higher.

### Using composer

#### Symfony >= 2.8 

    composer require triton/crud-generator

Add it to the `AppKernel.php` class:

    new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
    new Triton\Bundle\CrudBundle\TritonCrudBundle(),

Optionally for the bootstrap theme, add this to your `app/config/config.yml`
```yaml
twig:
    form_themes:
	- 'bootstrap_3_layout.html.twig' 

```

**This bundle works on Symfony 2.8 and >= 3.0 version.**


## Dependencies

This bundle extends [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle)  .
Pagination with [PagerFanta](https://github.com/whiteoctober/Pagerfanta/) and filter
support using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle).

## Usage

Use following command from console:

   php bin/console Triton:generate:crud

And follow the wizard steps.

### Available new options
The bundle adds few new steps to the command wizard compared to the doctrine crud generator.

* `--with-filter -  Whether or not to generate the filters.

* `--with-bulk-delete - Whether or not to generate bulk delete code.

* `--template - The base template name, which the views will override (by default TritonCrudBundle::base.html.twig).

* `--bundle-views - Whether or not to store the view files in app/Resources/views/ or in bundle's dir (default in app/Resources/views). It's not present in the wizard, but you can use it as parameter.

`--with-write options is enabled by default.

## Author

Petko Petkov - petkopara at gmail dot com


## License

CrudGeneratorBundle is licensed under the MIT License. See the LICENSE file for full details.