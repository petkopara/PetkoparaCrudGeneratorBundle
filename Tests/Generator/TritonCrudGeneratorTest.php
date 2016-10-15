<?php

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

namespace Petkopara\CrudGeneratorBundle\Tests\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Petkopara\CrudGeneratorBundle\Configuration\GeneratorAdvancedConfiguration;
use Petkopara\CrudGeneratorBundle\Generator\PetkoparaCrudGenerator;
use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Sensio\Bundle\GeneratorBundle\Tests\Generator\GeneratorTest;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class CrudGeneratorGeneratorTest extends GeneratorTest
{

    public function testGenerateYamlFull()
    {
        $advancedConfig = new GeneratorAdvancedConfiguration();
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'yml', '/post', true, true, $advancedConfig);
        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/config/routing/post.yml',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir . '/' . $file), sprintf('%s has been generated', $file));
        }
        $files = array(
            'Resources/config/routing/post.xml',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir . '/' . $file), sprintf('%s has not been generated', $file));
        }
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
            'public function newAction',
            'public function editAction',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }

        $this->assertPagination();
    }

    public function testGenerateXml()
    {
        $advancedConfig = new GeneratorAdvancedConfiguration();
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'xml', '/post', false, true, $advancedConfig);
        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/config/routing/post.xml',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir . '/' . $file), sprintf('%s has been generated', $file));
        }
        $files = array(
            'Resources/config/routing/post.yml',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir . '/' . $file), sprintf('%s has not been generated', $file));
        }
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            '@Route',
        );
        foreach ($strings as $string) {
            $this->assertNotContains($string, $content);
        }

        $this->assertPagination();
    }

    public function testGenerateAnnotationWrite()
    {
        $advancedConfig = new GeneratorAdvancedConfiguration();
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'annotation', '/post', true, true, $advancedConfig);
        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir . '/' . $file), sprintf('%s has been generated', $file));
        }
        $files = array(
            'Resources/config/routing/post.yml',
            'Resources/config/routing/post.xml',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir . '/' . $file), sprintf('%s has not been generated', $file));
        }
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
            'public function newAction',
            'public function editAction',
            '@Route',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }

        $this->assertPagination();
    }

    public function testGenerateAnnotation()
    {
        $advancedConfig = new GeneratorAdvancedConfiguration();
        $this->getGenerator()->generate($this->getBundle(), 'Post', $this->getMetadata(), 'annotation', '/post', false, true, $advancedConfig);
        $files = array(
            'Controller/PostController.php',
            'Tests/Controller/PostControllerTest.php',
            'Resources/views/post/index.html.twig',
            'Resources/views/post/show.html.twig',
            'Resources/views/post/new.html.twig',
            'Resources/views/post/edit.html.twig',
        );
        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir . '/' . $file), sprintf('%s has been generated', $file));
        }
        $files = array(
            'Resources/config/routing/post.yml',
            'Resources/config/routing/post.xml',
        );
        foreach ($files as $file) {
            $this->assertFalse(file_exists($this->tmpDir . '/' . $file), sprintf('%s has not been generated', $file));
        }
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            'namespace Foo\BarBundle\Controller;',
            'public function indexAction',
            'public function showAction',
            '@Route',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }

        $this->assertPagination();
    }

    public function testGenerateWithBaseTemplate()
    {
        
    }

    public function testGenerateWithBundleViews()
    {
        
    }

    public function testGenerateWithoutFilter()
    {
        
    }

    public function testGenerateWithoutDelete()
    {
        
    }

    /**
     * @dataProvider getRoutePrefixes
     */
    public function testGetRouteNamePrefix($original, $expected)
    {
        $prefix = DoctrineCrudGenerator::getRouteNamePrefix($original);
        $this->assertEquals($expected, $prefix);
    }

    public function getRoutePrefixes()
    {
        return array(
            array('', ''),
            array('/', ''),
            array('//', ''),
            array('/{foo}', ''),
            array('/{_foo}', ''),
            array('/{/foo}', ''),
            array('/{/foo/}', ''),
            array('/{_locale}', ''),
            array('/{_locale}/foo', 'foo'),
            array('/{_locale}/foo/', 'foo'),
            array('/{_locale}/foo/{_format}', 'foo'),
            array('/{_locale}/foo/{_format}/', 'foo'),
            array('/{_locale}/foo/{_format}/bar', 'foo_bar'),
            array('/{_locale}/foo/{_format}/bar/', 'foo_bar'),
            array('/{_locale}/foo/{_format}/bar//', 'foo_bar'),
            array('/{foo}/foo/{bar}/bar', 'foo_bar'),
            array('/{foo}/foo/{bar}/bar/', 'foo_bar'),
            array('/{foo}/foo/{bar}/bar//', 'foo_bar'),
        );
    }

    /**
     * 
     * @return PetkoparaCrudGenerator
     */
    protected function getGenerator()
    {
        $generator = new PetkoparaCrudGenerator($this->filesystem, $this->tmpDir);
        $generator->setSkeletonDirs(__DIR__ . '/../../Resources/skeleton');
        return $generator;
    }

    /**
     * @return BundleInterface
     */
    protected function getBundle()
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue($this->tmpDir));
        $bundle->expects($this->any())->method('getName')->will($this->returnValue('FooBarBundle'));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue('Foo\BarBundle'));
        return $bundle;
    }

    /**
     * @return ClassMetadataInfo
     */
    public function getMetadata()
    {
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')->disableOriginalConstructor()->getMock();
        $metadata->identifier = array('id');
        $metadata->fieldMappings = array('title' => array('type' => 'string'));
        return $metadata;
    }

    protected function assertPagination()
    {
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            'protected function paginator',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    protected function assertWithDelete()
    {
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            'public function deleteById',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

}
