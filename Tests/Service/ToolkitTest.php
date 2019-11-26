<?php

namespace NS\UtilBundle\Tests\Service;

use NS\UtilBundle\Service\Toolkit;
use PHPUnit\Framework\TestCase;

class ToolkitTest extends TestCase
{
    public function testPregReplace(): void
    {
        $input = 'something &amp; and &#123; another';
        $tookit = new Toolkit();
        $output = $tookit->stripTextAndHtmlEntities($input);
        $this->assertEquals('something--and--another',$output);
    }
}
