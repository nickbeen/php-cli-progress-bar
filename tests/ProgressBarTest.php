<?php

namespace NickBeen\ProgressBar\Tests;

use NickBeen\ProgressBar\ProgressBar;
use PHPUnit\Framework\TestCase;

class ProgressBarTest extends TestCase
{
    /**
     * Start output buffering
     */
    public function setUp(): void
    {
        ob_start();
    }

    /**
     * Flush and return output buffer
     */
    public function tearDown(): void
    {
        ob_get_flush();
    }

    /**
     * @test
     */
    public function it_can_progress_through_loop()
    {
        $progressBar = new ProgressBar();
        $progressBar->start();

        for ($i = 0; $i < 100; $i++) {
            $progressBar->tick();

            $this->assertEquals($i, $i);
        }

        $progressBar->finish();

        $this->assertEquals(0, $progressBar->getEstimatedTime());
    }

    /**
     * @test
     */
    public function it_can_progress_through_loop_halfway()
    {
        $progressBar = new ProgressBar();
        $progressBar->start();

        for ($i = 0; $i < 50; $i++) {
            $progressBar->tick();
        }

        $this->assertEquals(50, $progressBar->getProgress());

        $this->assertEquals(50, $progressBar->getPercentage());
    }

    /**
     * @test
     */
    public function it_can_display_empty_progressbar()
    {
        $progressBar = new ProgressBar();
        $progressBar->start();

        $this->assertEquals(0, $progressBar->getProgress());
    }

    /**
     * @test
     */
    public function it_can_iterate_through_array()
    {
        $iteration = [
             1 => 'Angel',
             2 => 'Bobby',
             3 => 'Clay',
             4 => 'Damon',
             5 => 'Ezekiel',
             6 => 'Felipe',
             7 => 'Gemma',
             8 => 'Happy',
             9 => 'Izzy',
            10 => 'Juice',
        ];

        $progressBar = new ProgressBar();
        //$progressBar->setMaxProgress(9);

        foreach ($progressBar->iterate($iteration) as $i => $s) {
            $this->assertEquals($i, $i);
            $this->assertIsString($s);
        }

        $this->assertEquals(10, $progressBar->getProgress());

        $this->assertEquals(100, $progressBar->getPercentage());
    }

    /**
     * @test
     */
    public function it_cannot_set_incorrect_progress()
    {
        $progressBar = new ProgressBar();
        $progressBar->setProgress(-1);

        $this->assertEquals(0, $progressBar->getProgress());

        $progressBar->setProgress(101);

        $this->assertEquals(101, $progressBar->getProgress());
    }

    /**
     * @test
     */
    public function it_cannot_set_incorrect_max_progress()
    {
        $progressBar = new ProgressBar();
        $progressBar->setMaxProgress(-1);

        $this->assertEquals(1, $progressBar->getMaxProgress());
    }
}
