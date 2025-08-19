<?php

namespace App\Ux;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

class MyChart
{
    public function __construct(private ChartBuilderInterface $chartBuilder) {}
    public function createChart(string $type): void {}
}
