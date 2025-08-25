<?php

namespace App\Tests\Helper;

use App\Helper\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    private ?ArrayHelper $arrayHelper;

    protected function setUp(): void
    {
        $this->arrayHelper = new ArrayHelper();
    }

    public function testMerge(): void
    {
        $arrayMergeExpected = ['test1', 'test2'];
        $array1 = ['test1'];
        $array2 = ['test2'];
        $arrayMergeActual = $this->arrayHelper->merge($array1, $array2);

        $this->assertSame($arrayMergeExpected, $arrayMergeActual);
    }

    protected function tearDown(): void
    {
        $this->arrayHelper = null;
    }
}
