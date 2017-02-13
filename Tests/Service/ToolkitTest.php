<?php

namespace NS\UtilBundle\Tests\Service;

use NS\UtilBundle\Service\Toolkit;

class ToolkitTest extends \PHPUnit_Framework_TestCase
{
    public function testPregReplace()
    {
        $input = "something &amp; and &#123; another";
        $tookit = new Toolkit();
        $output = $tookit->stripTextAndHtmlEntities($input);
        $this->assertEquals("something--and--another",$output);
    }
}
