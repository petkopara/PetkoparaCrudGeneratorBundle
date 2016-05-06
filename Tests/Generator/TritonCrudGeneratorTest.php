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

namespace Triton\Bundle\CrudBundle\Tests\Generator;

use Sensio\Bundle\GeneratorBundle\Tests\Generator\DoctrineCrudGeneratorTest;
use Triton\Bundle\CrudBundle\Generator\TritonCrudGenerator;

class TritonCrudGeneratorTest extends DoctrineCrudGeneratorTest
{
    protected function getGenerator()
    {
        $generator =  new TritonCrudGenerator($this->filesystem, $this->tmpDir);
        $generator->setSkeletonDirs(array(__DIR__.'/../../Resources/skeleton'));

        return $generator;
    }

    public function testGenerateYamlFull()
    {
        parent::testGenerateYamlFull();

        $this->assertFilterAndPaginator();
    }

    public function testGenerateXml()
    {
        parent::testGenerateXml();

        $this->assertFilterAndPaginator();
    }

    public function testGenerateAnnotationWrite()
    {
        parent::testGenerateAnnotationWrite();

        $this->assertFilterAndPaginator();
    }

    public function testGenerateAnnotation()
    {
        parent::testGenerateAnnotation();

        $this->assertFilterAndPaginator();
    }

    protected function assertFilterAndPaginator()
    {
        $content = file_get_contents($this->tmpDir . '/Controller/PostController.php');
        $strings = array(
            'protected function filter',
            'protected function paginator',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }


        $this->assertTrue(file_exists($this->tmpDir.'/Form/PostFilterType.php'));

        $content = file_get_contents($this->tmpDir.'/Form/PostFilterType.php');
//        $this->assertContains('->add(\'title\', \'filter_text\')', $content);
        $this->assertContains('class PostFilterType extends AbstractType', $content);
        $this->assertContains("'foo_barbundle_postfiltertype'", $content);

    }
}
