<?php

namespace Petkopara\CrudGeneratorBundle\Tests\Command;

use Petkopara\CrudGeneratorBundle\Configuration\Configuration;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Symfony\Component\Console\Tester\CommandTester;

/*
 * This file is part of the CrudGeneratorBundle
 *
 * It is based/extended from SensioGeneratorBundle
 *
 * (c) Petko Petkov <petkopara@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CrudGeneratorCommandTest extends GenerateCommandTest
{

    /**
     * @dataProvider getInteractiveCommandData
     */
    public function testInteractiveCommand($options, $input, $expected)
    {
        list($entity, $withoutWrite, $filterType, $template, $format, $prefix, $withoutShow, $withoutBulk, $withoutSort, $withoutPageSize, $bundleViews, $overwrite) = $expected;
        $advConfig = new Configuration();
        $advConfig->setWithoutWrite($withoutWrite);
        $advConfig->setRoutePrefix($prefix);
        $advConfig->setFormat($format);
        $advConfig->setFilterType($filterType);
        $advConfig->setBaseTemplate($template);
        $advConfig->setWithoutShow($withoutShow);
        $advConfig->setWithoutBulk($withoutBulk);
        $advConfig->setWithoutSorting($withoutSort);
        $advConfig->setWithoutPageSize($withoutPageSize);
        $advConfig->setOverwrite($overwrite);
        $advConfig->setBundleViews($bundleViews);

        $generator = $this->getGenerator();
        $generator
                ->expects($this->once())
                ->method('generateCrud')
                ->with($this->getBundle(), $entity, $this->getDoctrineMetadata(), $advConfig)
        ;
        $tester = new CommandTester($this->getCommand($generator, $input));
        $tester->execute($options);
    }

    public function getInteractiveCommandData()
    {
        return array(
            array(array(), "AcmeBlogBundle:Blog/Post\nn\ninput\nbase.html.twig\nannotation\n/foobar\n\n", array('Blog\\Post', false, 'input', 'base.html.twig', 'annotation', 'foobar', false, false, false, false, false, false)),
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post', '--bundle-views'=> true), '', array('Blog\\Post', false, 'form', 'PetkoparaCrudGeneratorBundle::base.html.twig', 'annotation', 'blog_post', false, false, false, false, true, false)),
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post', '--template'=> 'base.html.twig', '--filter-type'=>'none'), '', array('Blog\\Post', false, 'none', 'base.html.twig', 'annotation', 'blog_post', false, false, false, false, false, false)),
            array(array(), "AcmeBlogBundle:Blog/Post\nn\nform\nbase.html.twig\nyml\n/foobar\n\n", array('Blog\\Post', false, 'form', 'base.html.twig', 'yml', 'foobar', false, false, false, false, false, false)),
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post', '--format' => 'yml', '--route-prefix' => 'foo', '--without-write' => true, '--filter-type' => 'input'), '', array('Blog\\Post', true, 'input', 'PetkoparaCrudGeneratorBundle::base.html.twig', 'yml', 'foo', false, false, false, false, false, false)),
            array(array('--without-show' => true, '--without-bulk' => true,), "AcmeBlogBundle:Blog/Post\nn\ninput\nbase.html.twig\nannotation\n/foobar\n\n", array('Blog\\Post', false, 'input', 'base.html.twig', 'annotation', 'foobar', true, true, false, false, false, false)),
            array(array('--without-sorting' => true, '--without-page-size' => true, '--bundle-views'=> true), "AcmeBlogBundle:Blog/Post\nn\ninput\nbase.html.twig\nannotation\n/foobar\n\n", array('Blog\\Post', false, 'input', 'base.html.twig', 'annotation', 'foobar', false, false, true, true, true, false)),
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post', '--overwrite'=> true, '--without-sorting' => true, '--without-page-size' => true, '--without-show' => true, '--without-bulk' => true,), '', array('Blog\\Post', false, 'form', 'PetkoparaCrudGeneratorBundle::base.html.twig', 'annotation', 'blog_post', true, true, true, true, false, true)),
        );
    }

    /**
     * @dataProvider getNonInteractiveCommandData
     */
    public function testNonInteractiveCommand($options, $expected)
    {
        list($entity, $withoutWrite, $filterType, $template, $format, $prefix, $withoutShow, $withoutBulk, $withoutSort, $withoutPageSize, $bundleViews, $overwrite) = $expected;
        $advConfig = new Configuration();
        $advConfig->setWithoutWrite($withoutWrite);
        $advConfig->setRoutePrefix($prefix);
        $advConfig->setFormat($format);
        $advConfig->setFilterType($filterType);
        $advConfig->setBaseTemplate($template);
        $advConfig->setWithoutShow($withoutShow);
        $advConfig->setWithoutBulk($withoutBulk);
        $advConfig->setWithoutSorting($withoutSort);
        $advConfig->setWithoutPageSize($withoutPageSize);
        $advConfig->setBundleViews($bundleViews);
        $advConfig->setOverwrite($overwrite);
        $generator = $this->getGenerator();

        $generator
                ->expects($this->once())
                ->method('generateCrud')
                ->with($this->getBundle(), $entity, $this->getDoctrineMetadata(), $advConfig)
        ;
        $tester = new CommandTester($this->getCommand($generator, ''));
        $tester->execute($options, array('interactive' => false));
    }

    public function getNonInteractiveCommandData()
    {
        return array(
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post'), array('Blog\\Post', false, 'form', 'PetkoparaCrudGeneratorBundle::base.html.twig', 'annotation', 'blog_post', false, false, false, false, false, false)),
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post', '--filter-type'=> 'form', '--bundle-views'=> true, '--template'=> 'base.html.twig'), array('Blog\\Post', false, 'form', 'base.html.twig', 'annotation', 'blog_post', false, false, false, false, true, false)),
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post', '--format' => 'yml', '--route-prefix' => 'foo', '--without-write' => true), array('Blog\\Post', true, 'form', 'PetkoparaCrudGeneratorBundle::base.html.twig', 'yml', 'foo', false, false, false, false, false, false)),
            array(array('--entity' => 'AcmeBlogBundle:Blog/Post', '--without-show' => true, '--without-bulk' => true, '--without-sorting' => true, '--without-page-size' => true), array('Blog\\Post', false, 'form', 'PetkoparaCrudGeneratorBundle::base.html.twig', 'annotation', 'blog_post', true, true, true, true, false, false)),
        );
    }

    public function testCreateCrudWithAnnotationInNonAnnotationBundle()
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $routing = <<<DATA
acme_blog:
    resource: "@AcmeBlogBundle/Resources/config/routing.xml"
    prefix:   /
DATA;
        file_put_contents($rootDir . '/config/routing.yml', $routing);
        $options = array();
        $input = "AcmeBlogBundle:Blog/Post\nn\ninput\nPetkoparaCrudGeneratorBundle::base.html.twig\nannotation\n/foobar\n\n";
        $expected = array('Blog\\Post', 'annotation', 'foobar', false);
        list($entity, $format, $prefix, $withoutWrite) = $expected;
        $generator = $this->getGenerator();

        $advConfig = new Configuration();
        $advConfig->setWithoutWrite($withoutWrite);
        $advConfig->setRoutePrefix($prefix);
        $advConfig->setFormat($format);
        $generator
                ->expects($this->once())
                ->method('generateCrud')
                ->with($this->getBundle(), $entity, $this->getDoctrineMetadata(), $advConfig)
        ;
        $tester = new CommandTester($this->getCommand($generator, $input));
        $tester->execute($options);
        $expected = 'acme_blog_post:';
        $this->assertContains($expected, file_get_contents($rootDir . '/config/routing.yml'));
    }

    public function testCreateCrudWithAnnotationInAnnotationBundle()
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $routing = <<<DATA
acme_blog:
    resource: "@AcmeBlogBundle/Controller/"
    type:     annotation
DATA;
        file_put_contents($rootDir . '/config/routing.yml', $routing);
        $options = array();
        $input = "AcmeBlogBundle:Blog/Post\nn\ninput\nPetkoparaCrudGeneratorBundle::base.html.twig\nyml\n/foobar\n\n";
        $expected = array('Blog\\Post', 'yml', 'foobar', false);
        list($entity, $format, $prefix, $withoutWrite) = $expected;
        $advConfig = new Configuration();
        $advConfig->setRoutePrefix($prefix);
        $advConfig->setFormat($format);

        $generator = $this->getGenerator();
        $generator
                ->expects($this->once())
                ->method('generateCrud')
                ->with($this->getBundle(), $entity, $this->getDoctrineMetadata(), $advConfig)
        ;
        $tester = new CommandTester($this->getCommand($generator, $input));
        $tester->execute($options);
        $this->assertEquals($routing, file_get_contents($rootDir . '/config/routing.yml'));
    }

    public function testAddACrudWithOneAlreadyDefined()
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $routing = <<<DATA
acme_blog:
    resource: "@AcmeBlogBundle/Controller/OtherController.php"
    type:     annotation
DATA;
        file_put_contents($rootDir . '/config/routing.yml', $routing);
        $options = array();
        $input = "AcmeBlogBundle:Blog/Post\nn\ninput\nPetkoparaCrudGeneratorBundle::base.html.twig\nannotation\n/foobar\n\n";
        $expected = array('Blog\\Post', 'annotation', 'foobar', false, false, false, false, false);
        list($entity, $format, $prefix, $withoutWrite, $withoutShow, $withoutBulk, $withoutSort, $withoutPageSize) = $expected;
        $advConfig = new Configuration();

        $advConfig->setWithoutWrite($withoutWrite);
        $advConfig->setWithoutShow($withoutShow);
        $advConfig->setWithoutBulk($withoutBulk);
        $advConfig->setWithoutSorting($withoutSort);
        $advConfig->setWithoutPageSize($withoutPageSize);
        $advConfig->setRoutePrefix($prefix);
        $advConfig->setFormat($format);


        $generator = $this->getGenerator();
        $generator
                ->expects($this->once())
                ->method('generateCrud')
                ->with($this->getBundle(), $entity, $this->getDoctrineMetadata(), $advConfig)
        ;
        $tester = new CommandTester($this->getCommand($generator, $input));
        $tester->execute($options);
        $expected = '@AcmeBlogBundle/Controller/PostController.php';
        $this->assertContains($expected, file_get_contents($rootDir . '/config/routing.yml'));
    }

    /**
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function getCommand($generator, $input)
    {
        $command = $this
                ->getMockBuilder('Petkopara\CrudGeneratorBundle\Command\CrudGeneratorCommand')
                ->setMethods(array('getEntityMetadata'))
                ->getMock()
        ;
        $command
                ->expects($this->any())
                ->method('getEntityMetadata')
                ->will($this->returnValue(array($this->getDoctrineMetadata())))
        ;
        $command->setContainer($this->getContainer());
        $command->setHelperSet($this->getHelperSet($input));
        $command->setGenerator($generator);
        $command->setFormCrudGenerator($this->getFormGenerator());
        $command->setFilterGenerator($this->getFilterGenerator());
        return $command;
    }

    protected function getDoctrineMetadata()
    {
        return $this
                        ->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')
                        ->disableOriginalConstructor()
                        ->getMock()
        ;
    }

    protected function getGenerator()
    {
        // get a noop generator
        return $this
                        ->getMockBuilder('Petkopara\CrudGeneratorBundle\Generator\PetkoparaCrudGenerator')
                        ->disableOriginalConstructor()
                        ->setMethods(array('generateCrud'))
                        ->getMock()
        ;
    }

    protected function getFormGenerator()
    {
        return $this
                        ->getMockBuilder('Petkopara\CrudGeneratorBundle\Generator\PetkoparaFormGenerator')
                        ->disableOriginalConstructor()
                        ->setMethods(array('generate'))
                        ->getMock()
        ;
    }

    protected function getFilterGenerator()
    {
        return $this
                        ->getMockBuilder('Petkopara\CrudGeneratorBundle\Generator\PetkoparaFilterGenerator')
                        ->disableOriginalConstructor()
                        ->setMethods(array('generate'))
                        ->getMock()
        ;
    }

    protected function getBundle()
    {
        $bundle = parent::getBundle();
        $bundle
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('AcmeBlogBundle'))
        ;
        return $bundle;
    }

    protected function getContainer()
    {
        $container = parent::getContainer();
        $container->set('doctrine', $this->getDoctrine());
        return $container;
    }

    protected function getDoctrine()
    {
        $cache = $this->getMockBuilder('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver')->getMock();
        $cache
                ->expects($this->any())
                ->method('getAllClassNames')
                ->will($this->returnValue(array('Acme\Bundle\BlogBundle\Entity\Post')))
        ;
        $configuration = $this->getMockBuilder('Doctrine\ORM\Configuration')->getMock();
        $configuration
                ->expects($this->any())
                ->method('getMetadataDriverImpl')
                ->will($this->returnValue($cache))
        ;
        $configuration
                ->expects($this->any())
                ->method('getEntityNamespaces')
                ->will($this->returnValue(array('AcmeBlogBundle' => 'Acme\Bundle\BlogBundle\Entity')))
        ;
        $manager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')->getMock();
        $manager
                ->expects($this->any())
                ->method('getConfiguration')
                ->will($this->returnValue($configuration))
        ;
        $registry = $this->getMockBuilder('Symfony\Bridge\Doctrine\RegistryInterface')->getMock();
        $registry
                ->expects($this->any())
                ->method('getAliasNamespace')
                ->will($this->returnValue('Acme\Bundle\BlogBundle\Entity\Blog\Post'))
        ;
        $registry
                ->expects($this->any())
                ->method('getManager')
                ->will($this->returnValue($manager))
        ;
        return $registry;
    }

}
