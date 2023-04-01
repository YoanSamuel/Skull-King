<?php

namespace App\Tests\Controller;

use App\Controller\DefaultController;
use PHPUnit\Framework\TestCase;

class DefaultControllerTest extends TestCase
{
    public function test_first()
    {
        $this->assertEquals(true, true);
    }
}
