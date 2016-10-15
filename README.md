# CrudGeneratorBundle
Symfony3 CRUD generator bundle with pagination, filter, bulk actions and Twitter bootstrap 3.3.6 markup.
Extends the functionality of [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle) with additional options and features.

[![Build Status](https://travis-ci.org/petkopara/CrudGeneratorBundle.svg?branch=master)](https://travis-ci.org/petkopara/CrudGeneratorBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/7d24085a-9a27-4607-adf5-efe1bb39f62b/mini.png)](https://insight.sensiolabs.com/projects/7d24085a-9a27-4607-adf5-efe1bb39f62b)
[![Latest Stable](https://img.shields.io/packagist/v/triton/crud-generator.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/triton/crud-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/triton/crud-generator.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/triton/crud-generator)

## Features
* Pagination
* Filtering 
* Doctrine association mapping support(Many-to-One, One-to-Many, One-to-One and Many-to-Many) in forms and filters
* Bulk actions(delete) on multiple rows
* Delete from index
* Set your base template in the generated views.
* Possibility to set the save path for all the generated files (by default in app/Resources).
* Filtering, bulk and write are optional.

## Screenshot

![Screenshot](https://raw.github.com/petkopara/CrudGeneratorBundle/master/screenshot.png "Screenshot")

## Installation
This bundle is compatible with Symfony 2.8/3.0 or higher.

### Using composer

#### Symfony >= 2.8 

    composer require petkopara/crud-generator-bundle

Add it to the `AppKernel.php` class:

    new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
    new Petkopara\CrudGeneratorBundle\PetkoparaCrudGeneratorBundle(),

Optionally for the bootstrap theme, add this to your `app/config/config.yml`
```yaml
twig:
    form_themes:
	- 'bootstrap_3_layout.html.twig' 

```

Install the assets.
```sh
php bin/console assets:install --symlink
```
Optionally if you are using your own base tempalte, be sure that you include the javascript file of the bundle in your base template.
<script src="{{asset("bundles/tritoncrud/js/petkopara-crud-generator.min.js")}}"></script>

## Dependencies

This bundle extends [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle). 
Pagination with [PagerFanta](https://github.com/whiteoctober/Pagerfanta/) and filter
support using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle).

## Usage

Use the following command from console:
```sh
php bin/console petkopara:generate:crud
```
And follow the wizard steps.

### Available new options
The bundle adds few new parameters to the generate command compared to the doctrine crud generator.

* `--with-filter` -  To generate the filters.

* `--with-bulk` - To generate bulk actions code.

* `--template` - The base template name, which the views will override. For example set it to `--template=base.html.twig` to extends your base template.(by default CrudGeneratorBundle::base.html.twig).

* `--bundle-views` - Whether to store the view files in the bundles dir. By default the vies are stored in _app/Resources/views/_. It's not present in the wizard, but can be used as parameter.

`--with-write` options is enabled by default.

## Author

Petko Petkov - petkopara@gmail.com


## License

CrudGeneratorBundle is licensed under the MIT License.
