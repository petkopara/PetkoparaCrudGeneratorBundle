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
use Petkopara\CrudGeneratorBundle\Command\CrudGeneratorCommand;
use Petkopara\CrudGeneratorBundle\Generator\PetkoparaFilterGenerator;
use Sensio\Bundle\GeneratorBundle\Tests\Generator\GeneratorTest;

class PetkoparaFilterGeneratorTest extends GeneratorTest
{

    public function testGenerateForm()
    {
        $this->generateFormFilter(false);
        $this->assertTrue(file_exists($this->tmpDir . '/Form/PostFilterType.php'));
        $content = file_get_contents($this->tmpDir . '/Form/PostFilterType.php');

        $this->assertContains("->add('title', Filters\TextFilterType::class)", $content);
        $this->assertContains("->add('createdAt', Filters\DateFilterType::class)", $content);
        $this->assertContains("->add('publishedAt', Filters\TextFilterType::class)", $content);
        $this->assertContains("->add('updatedAt', Filters\DateTimeFilterType::class)", $content);
        $this->assertContains("->add('parent', Filters\EntityFilterType::class", $content);
        $this->assertContains("'class' => 'FooBundle\Entity\Parent',", $content);
        $this->assertContains("'choice_label' => 'name',", $content);
        $this->assertContains('class PostFilterType extends AbstractType', $content);
        if (!method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $this->assertContains('getName', $content);
            $this->assertContains("'foo_barbundle_post'", $content);
        } else {
            $this->assertNotContains('getName', $content);
            $this->assertNotContains("'foo_barbundle_post'", $content);
        }
    }

    public function testGenerateMultiSearch()
    {
        $this->generateMultiSearchFilter(false);
        $this->assertTrue(file_exists($this->tmpDir . '/Form/PostFilterType.php'));
        $content = file_get_contents($this->tmpDir . '/Form/PostFilterType.php');

        $this->assertContains("->add('search', MultiSearchType::class", $content);
        $this->assertContains('class PostFilterType extends AbstractType', $content);
        if (!method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $this->assertContains('getName', $content);
            $this->assertContains("'foo_barbundle_post'", $content);
        } else {
            $this->assertNotContains('getName', $content);
            $this->assertNotContains("'foo_barbundle_post'", $content);
        }
    }

    private function generateFormFilter($overwrite)
    {

        $generator = $this->getFilterGenerator();
        $bundle = $this->getBundle();
        $metadata = $this->getMetadata();
        $generator->generate($bundle, 'Post', $metadata, $overwrite, CrudGeneratorCommand::FILTER_TYPE_FORM);
    }

    private function generateMultiSearchFilter($overwrite)
    {
        $generator = $this->getFilterGenerator();
        $bundle = $this->getBundle();
        $metadata = $this->getMetadata();
        $generator->generate($bundle, 'Post', $metadata, $overwrite, CrudGeneratorCommand::FILTER_TYPE_INPUT);
    }

    private function getFilterGenerator()
    {

        $guesser = $this->getMockBuilder('Petkopara\CrudGeneratorBundle\Generator\Guesser\MetadataGuesser')
                ->setMethods(array('guessChoiceLabelFromClass'))
                ->disableOriginalConstructor()
                ->getMock();
        $guesser->expects($this->any())->method('guessChoiceLabelFromClass')->will($this->returnValue('name'));

        $generator = new PetkoparaFilterGenerator($guesser);
        $generator->setSkeletonDirs(array(__DIR__ . '/../../Resources/skeleton'));

        return $generator;
    }

    private function getMetadata()
    {
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')->disableOriginalConstructor()->getMock();
        $metadata->identifier = array('id');
        $metadata->fieldMappings = array(
            'title' => array('type' => 'string'),
            'createdAt' => array('type' => 'date'),
            'publishedAt' => array('type' => 'time'),
            'updatedAt' => array('type' => 'datetime'),
        );
        $metadata->associationMappings = array(
            'parent' => array('type' => ClassMetadataInfo::MANY_TO_ONE, 'targetEntity' => 'FooBundle\Entity\Parent'),
        );
        return $metadata;
    }

    private function getBundle()
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue($this->tmpDir));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue('Foo\BarBundle'));
        return $bundle;
    }

}
