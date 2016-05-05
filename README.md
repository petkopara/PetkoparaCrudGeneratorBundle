# CrudGeneratorBundle
Symfony3 CRUD generator bundle with pagination, filter, bulk actions and Twitter bootstrap 3.3.6 features.
It's highly customizable and flexible. 

[![Build Status](https://travis-ci.org/petkopara/TritonCrudBundle.svg?branch=master)](https://travis-ci.org/petkopara/TritonCrudBundle)


## Screenshot

![Screenshot](https://raw.github.com/Triton/CrudGeneratorBundle/master/screenshot.png "Screenshot")

## Installation
This bundle is compatible with Symfony 2.8/3.0 or higher.

### Using composer

Add following lines to your `composer.json` file:

#### Symfony >= 2.8 

    "require": {
      ...
      "Triton/crud-generator": "dev-master"
    },


Execute:

    php composer.phar update

Add it to the `AppKernel.php` class:

    new Lexik\Bundle\FormFilterBundle\LexikFormFilterBundle(),
    new Petkopara\Bundle\Crud\PetkoparaTritonCrudBundle(),

Add it to your `app/config/config.yml`

    twig:
        form_themes:
			- 'bootstrap_3_layout.html.twig' 

**This bundle works on Symfony 2.8 and >= 3.0 version.**


## Dependencies

This bundle extends [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle) and add a paginator using [PagerFanta](https://github.com/whiteoctober/Pagerfanta/) and filter
support using [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle).

## Usage

Use following command from console:

    app/console Triton:generate:crud

As you will see there is no config file. You will generate a CRUD code with all fields from your entity. But after code generation you
are free to modify the code because there is no magic just a simple code that is very easy to understand.

You have to know that if you reuse the command to recreate same entity, first you must delete controller and form files
from previous generation.

## Author

Petko Petkov - 


## License

CrudGeneratorBundle is licensed under the MIT License. See the LICENSE file for full details.