<?php

namespace Lyra\Tests\View;

use Lyra\View\LyraEngine;
use PHPUnit\Framework\TestCase;

class LyraEngineTest extends TestCase {
    public function test_renders_template_with_parameters() {
        $param1 = "Test 1";
        $param2 = 2;

        $expectedHTML = "
            <html>
                <body>
                    <h1>$param1</h1>
                    <h1>$param2</h1>
                </body>
            </html>
        ";

        $engine = new LyraEngine(__DIR__ . "/views");

        $content = $engine->render("test", compact('param1', 'param2'), 'layout');

        $this->assertEquals(
            preg_replace("/\s*/", "", $expectedHTML),
            preg_replace("/\s*/", "", $content),
        );
    }
}
