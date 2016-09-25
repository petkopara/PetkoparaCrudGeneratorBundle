# TritonCrudBundle
Symfony3 CRUD generator bundle with pagination, filter, bulk delete and Twitter bootstrap 3.3.6 markup.
Extends the functionality of [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle) with additional options and features.

[![Build Status](https://travis-ci.org/petkopara/TritonCrudBundle.svg?branch=master)](https://travis-ci.org/petkopara/TritonCrudBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7d24085a-9a27-4607-adf5-efe1bb39f62b/mini.png)](https://insight.sensiolabs.com/projects/7d24085a-9a27-4607-adf5-efe1bb39f62b)
[![Latest Stable Version](https://poser.pugx.org/triton/crud-generator/v/stable)](https://packagist.org/packages/triton/crud-generator)
[![Total Downloads](https://poser.pugx.org/triton/crud-generator/downloads)](https://packagist.org/packages/triton/crud-generator)
[![License](https://poser.pugx.org/triton/crud-generator/license)](https://packagist.org/packages/triton/crud-generator)

## Features
* Pagination
* Filtering 
* Bulk actions(delete) on multiple rows
* Delete from index
* Set your base template in the generated views.
* Possibility to set the save path for all the generated files (by default in app/Resources).
* Filtering, bulk and write are optional.

## Screenshot

![Screenshot](https://raw.github.com/petkopara/TritonCrudBundle/master/screenshot.png "Screenshot")

## Installation
This bundle is compatible with Symfony 2.8/3.0 or higher.

### Using composer

#### Symfony >= 2.8 

    composer require triton/crud-generator

Add it to the `AppKernel.php` class:

    new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
    new Petkopara\TritonCrudBundle\PetkoparaTritonCrudBundle(),

Optionally for the bootstrap theme, add this to your `app/config/config.yml`
```yaml
twig:
    form_themes:
	- 'bootstrap_3_layout.html.twig' 

```

If you are using the triton base.html.twig for base  template, install the assets.
```sh
php bin/console assets:install --symlink
```
 

## Dependencies

This bundle extends [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle). 
Pagination with [PagerFanta](https://github.com/whiteoctober/Pagerfanta/) and filter
support using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle).

## Usage

Use the following command from console:
```sh
php bin/console triton:generate:crud
```
And follow the wizard steps.

### Available new options
The bundle adds few new parameters to the generate command compared to the doctrine crud generator.

* `--with-filter` -  To generate the filters.

* `--with-bulk-delete` - To generate bulk delete code.

* `--template` - The base template name, which the views will override. For example set it to `--template=base.html.twig` to extends your base template.(by default TritonCrudBundle::base.html.twig).

* `--bundle-views` - Whether to store the view files in the bundles dir. By default the vies are stored in _app/Resources/views/_. It's not present in the wizard, but can be used as parameter.

`--with-write` options is enabled by default.

## Author

Petko Petkov - petkopara@gmail.com


## License

TritonCrudBundle is licensed under the MIT License.
