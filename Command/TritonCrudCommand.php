<?php

/*
 * This file is part of the CrudBundle
 *
 * It is based/extended from SensioGeneratorBundle
 *
 * (c) Petko Petkov <petkopara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Triton\Bundle\CrudBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\AutoComplete\EntitiesAutoCompleter;
use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Triton\Bundle\CrudBundle\Generator\TritonCrudGenerator;

class TritonCrudCommand extends GenerateDoctrineCrudCommand {

    protected $generator;
    protected $formGenerator;

    protected function configure() {

        $this
                ->setName('triton:generate:crud')
                ->setDescription('A CRUD generator with pagination, filters, bulk delete and bootstrap markdown.')
                ->setDefinition(array(
                    new InputArgument('entity', InputArgument::OPTIONAL, 'The entity class name to initialize (shortcut notation)'),
                    new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)'),
                    new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'The route prefix'),
                    new InputOption('template', '', InputOption::VALUE_REQUIRED, 'The base template which will be extended by the templates', 'TritonCrudBundle::base.html.twig'),
                    new InputOption('format', '', InputOption::VALUE_REQUIRED, 'The format used for configuration files (php, xml, yml, or annotation)', 'annotation'),
                    new InputOption('overwrite', '', InputOption::VALUE_NONE, 'Overwrite any existing controller or form class when generating the CRUD contents'),
                    new InputOption('bundle-views', '', InputOption::VALUE_NONE, 'Whether or not to store the view files in app/Resources/views/ or in bundle dir'),
                    new InputOption('with-write', '', InputOption::VALUE_NONE, 'Whether or not to generate create, new and delete actions'),
                    new InputOption('with-filter', '', InputOption::VALUE_NONE, 'Whether or not to generate filters '),
                    new InputOption('with-bulk-delete', '', InputOption::VALUE_NONE, 'Whether or not to generate bulk delete')))
                ->setHelp(<<<EOT
The <info>%command.name%</info> command generates a CRUD based on a Doctrine entity.

The default command only generates the list and show actions.

<info>php %command.full_name% --entity=AcmeBlogBundle:Post --route-prefix=post_admin</info>

Using the --with-write option allows to generate the new, edit and delete actions.
                
Using the --bundle-views option store the view files in the bundles dir.
                
Using the --with-bulk-delete=false  option to not generate bulk delete.
                
Using the --template option allows to set base template from which the crud views to overide.
    
<info>php %command.full_name% doctrine:generate:crud --entity=AcmeBlogBundle:Post --route-prefix=post_admin --with-write</info>

Every generated file is based on a template. There are default templates but they can be overridden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/TritonCrudBundle/skeleton/crud
APP_PATH/Resources/TritonCrudBundle/skeleton/crud</info>

And

<info>__bundle_path__/Resources/TritonCrudBundle/skeleton/form
__project_root__/app/Resources/TritonCrudBundle/skeleton/form</info>

EOT
        );
    }

    protected function createGenerator($bundle = null) {
        return new TritonCrudGenerator($this->getContainer()->get('filesystem'), $this->getContainer()->get('kernel')->getRootDir());
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null) {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath() . '/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir() . '/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@TritonCrudBundle/Resources/skeleton');
        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@TritonCrudBundle/Resources');

        return $skeletonDirs;
    }

    protected function interact(InputInterface $input, OutputInterface $output) {

        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Triton CRUD generator');

        // namespace
        $output->writeln(array(
            '',
            'This command helps you generate CRUD controllers and templates.',
            '',
            'First, give the name of the existing entity for which you want to generate a CRUD',
            '(use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>)',
            '',
        ));

        if ($input->hasArgument('entity') && $input->getArgument('entity') != '') {
            $input->setOption('entity', $input->getArgument('entity'));
        }

        $question = new Question($questionHelper->getQuestion('The Entity shortcut name', $input->getOption('entity')), $input->getOption('entity'));
        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'));


        $autocompleter = new EntitiesAutoCompleter($this->getContainer()->get('doctrine')->getManager());
        $autocompleteEntities = $autocompleter->getSuggestions();
        $question->setAutocompleterValues($autocompleteEntities);
        $entity = $questionHelper->ask($input, $output, $question);

        $input->setOption('entity', $entity);
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        try {
            $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle) . '\\' . $entity;
            $this->getEntityMetadata($entityClass);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Entity "%s" does not exist in the "%s" bundle. You may have mistyped the bundle name or maybe the entity doesn\'t exist yet (create it first with the "doctrine:generate:entity" command).', $entity, $bundle));
        }

        // write?
        $withWrite = $input->getOption('with-write') ? : true;
        $output->writeln(array(
            '',
            'By default, the generator creates all actions: list and show, new, update, and delete.',
            'You can also ask it to generate only "list and show" actions:',
            '',
        ));
        $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you want to generate the "write" actions', $withWrite ? 'yes' : 'no', '?', $withWrite), $withWrite);
        $withWrite = $questionHelper->ask($input, $output, $question);
        $input->setOption('with-write', $withWrite);


        // filters?
        $withFilter = $input->getOption('with-filter') ? : true;
        $output->writeln(array(
            '',
            'By default, the generator creates filter',
            '',
        ));
        $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you want to generate filter', $withFilter ? 'yes' : 'no', '?', $withFilter), $withFilter);
        $withFilter = $questionHelper->ask($input, $output, $question);
        $input->setOption('with-filter', $withFilter);
        
        //bulk delete
        if ($withWrite == true) {
            $withBulkDelete = $input->getOption('with-bulk-delete') ? : true;
            $output->writeln(array(
                '',
                'By default, the generator creates bulk delete ',
                '',
            ));
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you want to generate bulk delete', $withBulkDelete ? 'yes' : 'no', '?', $withBulkDelete), $withBulkDelete);
            $withBulkDelete = $questionHelper->ask($input, $output, $question);
            $input->setOption('with-bulk-delete', $withBulkDelete);
        } else {
            $withBulkDelete = false;
        }

        // template?
        $template = $input->getOption('template');
        $output->writeln(array(
            '',
            'By default, the created views extends the TritonCrudBundle::base.html.twig',
            'You can also set your template which the views to extend, for example base.html.twig ',
            '',
        ));
        $question = new Question($questionHelper->getQuestion('Base template for the views', $template), $template);
        $format = $questionHelper->ask($input, $output, $question);
        $input->setOption('template', $template);


        // format
        $format = $input->getOption('format');
        $output->writeln(array(
            '',
            'Determine the format to use for the generated CRUD.',
            '',
        ));
        $question = new Question($questionHelper->getQuestion('Configuration format (yml, xml, php, or annotation)', $format), $format);
        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat'));
        $format = $questionHelper->ask($input, $output, $question);
        $input->setOption('format', $format);

        // route prefix
        $prefix = $this->getRoutePrefix($input, $entity);
        $output->writeln(array(
            '',
            'Determine the routes prefix (all the routes will be "mounted" under this',
            'prefix: /prefix/, /prefix/new, ...).',
            '',
        ));
        $prefix = $questionHelper->ask($input, $output, new Question($questionHelper->getQuestion('Routes prefix', '/' . $prefix), '/' . $prefix));
        $input->setOption('route-prefix', $prefix);

        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf('You are going to generate a CRUD controller for "<info>%s:%s</info>"', $bundle, $entity),
            sprintf('using the "<info>%s</info>" format.', $format),
            '',
        ));
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');
                return 1;
            }
        }

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $format = Validators::validateFormat($input->getOption('format'));
        $prefix = $this->getRoutePrefix($input, $entity);
        $withWrite = $input->getOption('with-write');
        $withFilter = $input->getOption('with-filter');
        $withBulkDelete = $input->getOption('with-bulk-delete');

        $template = $input->getOption('template');
        $bundleViews = $input->getOption('bundle-views');

        $forceOverwrite = $input->getOption('overwrite');

        $questionHelper->writeSection($output, 'CRUD generation');

        try {
            $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle) . '\\' . $entity;
            $metadata = $this->getEntityMetadata($entityClass);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf('Entity "%s" does not exist in the "%s" bundle. Create it with the "doctrine:generate:entity" command and then execute this command again.', $entity, $bundle));
        }

        $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);

        $generator = $this->getGenerator($bundle);

        $generator->generate($bundle, $entity, $metadata[0], $format, $prefix, $withWrite, $forceOverwrite, $template, $bundleViews, $withFilter, $withBulkDelete);

        $output->writeln('Generating the CRUD code: <info>OK</info>');

        $errors = array();
        $runner = $questionHelper->getRunner($output, $errors);

        // form
        if ($withWrite) {
            $this->generateForm($bundle, $entity, $metadata, $forceOverwrite);
            $output->writeln('Generating the Form code: <info>OK</info>');
        }

        // routing
        $output->write('Updating the routing: ');
        if ('annotation' != $format) {
            $runner($this->updateRouting($questionHelper, $input, $output, $bundle, $format, $entity, $prefix));
        } else {
            $runner($this->updateAnnotationRouting($bundle, $entity, $prefix));
        }

        $questionHelper->writeGeneratorSummary($output, $errors);
    }

}
