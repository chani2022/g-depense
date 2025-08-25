<?php

namespace App\Helper;

class ArrayHelper
{
    public function merge(array ...$arrays): array
    {
        return array_merge(...$arrays);
    }
}
