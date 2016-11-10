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
use Petkopara\CrudGeneratorBundle\Generator\PetkoparaFormGenerator;
use Sensio\Bundle\GeneratorBundle\Tests\Generator\GeneratorTest;

class PetkoparaFormGeneratorTest extends GeneratorTest
{

    public function testGenerate()
    {
        $this->generateForm(false);
        $this->assertTrue(file_exists($this->tmpDir . '/Form/PostType.php'));
        $content = file_get_contents($this->tmpDir . '/Form/PostType.php');
        $this->assertContains('->add(\'title\')', $content);
        $this->assertContains('->add(\'createdAt\')', $content);
        $this->assertContains('->add(\'publishedAt\')', $content);
        $this->assertContains('->add(\'updatedAt\')', $content);
        $this->assertContains('->add(\'parent\')', $content);
        $this->assertContains("'class' => 'FooBundle\Entity\Parent'", $content);
        $this->assertContains("'choice_label' => 'name'", $content);
        $this->assertContains('class PostType extends AbstractType', $content);
        $this->assertContains("'data_class' => 'Foo\BarBundle\Entity\Post'", $content);
        if (!method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $this->assertContains('getName', $content);
            $this->assertContains("'foo_barbundle_post'", $content);
        } else {
            $this->assertNotContains('getName', $content);
            $this->assertNotContains("'foo_barbundle_post'", $content);
        }
    }

    /**
     * @param boolean $overwrite
     */
    private function generateForm($overwrite)
    {

        $guesser = $this->getMockBuilder('Petkopara\CrudGeneratorBundle\Generator\Guesser\MetadataGuesser')
                ->setMethods(array('guessChoiceLabelFromClass'))
                ->disableOriginalConstructor()
                ->getMock();
        $guesser->expects($this->any())->method('guessChoiceLabelFromClass')->will($this->returnValue('name'));

        $generator = new PetkoparaFormGenerator($guesser);
        $generator->setSkeletonDirs(array(__DIR__ . '/../../Resources/skeleton'));

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
            'parent' => array('type' => ClassMetadataInfo::MANY_TO_ONE, 'isOwningSide'=> true ,'targetEntity' => 'FooBundle\Entity\Parent'),
        );
        $metadata->fieldNames = array(
            'title' => 'title',
            'createdAt' => 'createdAt',
            'publishedAt' => 'publishedAt',
            'updatedAt' => 'updatedAt',
            'parent' => 'parent',
        );
        $metadata->associationMappings = $metadata->fieldMappings;

        $generator->generate($bundle, 'Post', $metadata, $overwrite);
    }

}
