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
use Petkopara\CrudGeneratorBundle\Generator\PetkoparaFilterGenerator;
use Sensio\Bundle\GeneratorBundle\Tests\Generator\GeneratorTest;

class PetkoparaFilterGeneratorTest extends GeneratorTest
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

    private function generateFilter($overwrite)
    {

        $metadataFactory = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('getClassMetadata', 'getMetadata'))
            ->getMock();
        $obj = new \stdClass();
        $obj->fieldMappings = array('name' => array('type' => 'string'));
        $metadataFactory->expects($this->any())->method('getMetadata')->will($this->returnValue(array($obj)));
        $metadataFactory->expects($this->any())
            ->method($this->anything())  // all other calls return self
            ->will($this->returnSelf());

        $generator = new PetkoparaFilterGenerator($metadataFactory);
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
        $metadata->associationMappings = array(
            'parent' => array('type' => ClassMetadataInfo::MANY_TO_ONE, 'targetEntity' => 'FooBundle\Entity\Parent'),
        );

        $generator->generate($bundle, 'Post', $metadata, $overwrite);
    }
}
