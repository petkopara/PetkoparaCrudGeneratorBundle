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

namespace Petkopara\TritonCrudBundle\Tests\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Petkopara\TritonCrudBundle\Generator\TritonFilterGenerator;
use Sensio\Bundle\GeneratorBundle\Tests\Generator\GeneratorTest;

class TritonFilterGeneratorTest extends GeneratorTest
{

    public function testGenerate()
    {
        $this->generateFilter(false);
        $this->assertTrue(file_exists($this->tmpDir . '/Form/PostFilterType.php'));
        $content = file_get_contents($this->tmpDir . '/Form/PostFilterType.php');

        $this->assertContains("->add('title', Filters\TextFilterType::class)", $content);
        $this->assertContains("->add('createdAt', Filters\DateFilterType::class)", $content);
        $this->assertContains("->add('publishedAt', Filters\TextFilterType::class)", $content);
        $this->assertContains("->add('updatedAt', Filters\DateTimeFilterType::class)", $content);
        $this->assertContains('class PostFilterType extends AbstractType', $content);
        if (!method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $this->assertContains('getName', $content);
            $this->assertContains("'foo_barbundle_post'", $content);
        } else {
            $this->assertNotContains('getName', $content);
            $this->assertNotContains("'foo_barbundle_post'", $content);
        }
    }

    private function generateFilter($overwrite)
    {

        $generator = new TritonFilterGenerator();
        $generator->setSkeletonDirs(__DIR__ . '/../../Resources/skeleton');

        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $bundle->expects($this->any())->method('getPath')->will($this->returnValue($this->tmpDir));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue('Foo\BarBundle'));
        
        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')->disableOriginalConstructor()->getMock();
        $metadata->identifier = array('id');
        $metadata->fieldMappings = array(
            'title' => array('type' => 'string'),
            'createdAt' => array('type' => 'date'),
            'publishedAt' => array('type' => 'time'),
            'updatedAt' => array('type' => 'datetime'),
        );
        $metadata->associationMappings = $metadata->fieldMappings;
        
        $generator->generate($bundle, 'Post', $metadata, $overwrite);
    }

}
