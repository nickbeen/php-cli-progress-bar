<?php

namespace NickBeen\ProgressBar;

/**
 * For creating minimal progress bars in PHP CLI scripts.
 */
final class ProgressBar
{
    /** Character to display current progress in progress bar */
    private string $barCharacter = '#';

    /** Width of progress bar, in number of characters */
    private int $barWidth = 28;

    /** Minimum time in seconds between consecutive refreshes of display */
    private float $drawFrequency = 0.25;

    /** Character to display remaining progress in progress bar */
    private string $emptyBarCharacter = '.';

    /** Estimated time to finish in seconds */
    private int $estimatedTime = 0;

    /** Timing of last display */
    private float $lastDisplayTime = 0.0;

    /** Current progress in percentage */
    private int $percentage = 0;

    /** Time when progress bar starts displaying  */
    private int $startTime;

    /**
     * Indicator whether maxProgress was truly set as zero.
     * MaxProgress must always be at least one to be displayed.
     */
    private bool $maxProgressIsZero = false;

    /**
     * Initialize and set progress and maxProgress when available.
     * Use separate setProgress() and setMaxProgress() methods when undetermined during initialization.
     *
     * @param int|float $progress Current progress
     * @param int|float $maxProgress Maximum progress
     */
    public function __construct(
        private int|float $progress = 0,
        private int|float $maxProgress = 100,
    ) {
        $this->startTime = time();

        /** setMaxProgress MUST go before setProgress */
        $this->setMaxProgress($maxProgress)
            ->setProgress($progress);
    }

    /**
     * Move cursor to line start and clear whole line.
     */
    private function clearLine(): void
    {
        echo "\x1b[2K";
    }

    /**
     * Private method used for displaying the progress bar.
     */
    private function display(): ProgressBar
    {
        if ($this->percentage == 100) {
            $this->clearLine();
        }

        $barCompleteWidth = ceil($this->barWidth * ($this->percentage / 100));
        $barIncompleteWidth = floor($this->barWidth - $barCompleteWidth);

        echo sprintf(
            '%s%s/%d [%s%s] %s%%',
            "\r",
            str_pad(string: $this->getProgress(), length: strlen((string) $this->getMaxProgress()), pad_type: STR_PAD_LEFT),
            $this->getMaxProgress(),
            str_repeat($this->barCharacter, $barCompleteWidth),
            str_repeat($this->emptyBarCharacter, $barIncompleteWidth),
            str_pad(string: $this->getPercentage(), length: 3, pad_type: STR_PAD_LEFT)
        );

        /** Remove estimated time from display when done */
        if ($this->percentage < 100) {
            echo sprintf(
                ' (%02d:%02d:%02d)',
                floor($this->estimatedTime / 3600),
                floor($this->estimatedTime / 60) % 60,
                floor($this->estimatedTime) % 60
            );
        }

        return $this;
    }

    /**
     * Display a fully completed progress bar to ensure the progress bar reaches 100% completion.
     */
    public function finish(): void
    {
        $this->setProgress($this->maxProgress)
            ->display()
            ->showCursor()
            ->insertNewLine();
    }

    /**
     * Get estimated time for progress bar to complete.
     */
    public function getEstimatedTime(): string
    {
        return $this->estimatedTime;
    }

    /**
     * Get maximum progress.
     */
    public function getMaxProgress(): int
    {
        return round($this->maxProgress);
    }

    /**
     * Get current progress in percentage.
     */
    public function getPercentage(): string
    {
        return $this->percentage;
    }

    /**
     * Get current progress.
     */
    public function getProgress(): int
    {
        return round($this->progress);
    }

    /**
     * Hide cursor after starting progress bar
     */
    private function hideCursor(): ProgressBar
    {
        /** Show cursor again in case of any exit code */
        register_shutdown_function(function () {
            $this->showCursor();
        });

        echo "\x1b[?25l";

        return $this;
    }

    /**
     * Move cursor to new line.
     */
    private function insertNewLine(): void
    {
        echo PHP_EOL;
    }

    /**
     * Iterate through countable variables such as an array.
     */
    public function iterate(\Countable|iterable $iterable): iterable
    {
        $this->setMaxProgress(is_countable($iterable) ? count($iterable) : 0);

        $this->start();

        foreach ($iterable as $key => $value) {
            yield $key => $value;

            $this->tick();
        }

        $this->finish();
    }

    /**
     * Change default character for completed progress
     */
    public function setBarCharacter(string $character): ProgressBar
    {
        $this->barCharacter = $character;

        return $this;
    }

    /**
     * Change default character for incomplete progress.
     */
    public function setEmptyBarCharacter(string $character): ProgressBar
    {
        $this->emptyBarCharacter = $character;

        return $this;
    }

    /**
     * Calculate estimated time for completion of progress bar.
     */
    private function setEstimatedTime(): ProgressBar
    {
        $this->estimatedTime = round(
            (
                time() - $this->startTime
            ) / max(1, $this->progress) * ($this->maxProgress - $this->progress)
        );

        return $this;
    }

    /**
     * Manually adjust the maxProgress parameter when undetermined or changed.
     */
    public function setMaxProgress(int|float $maxProgress): ProgressBar
    {
        if ($maxProgress == 0) {
            $this->maxProgressIsZero = true;
        }

        $this->maxProgress = max(1, $maxProgress);

        return $this;
    }

    /**
     * Calculate completion in percentage
     */
    private function setPercentage(): void
    {
        $percentage = ($this->progress / $this->maxProgress) * 100;
        $this->percentage = number_format(round($percentage), 2);
    }

    /**
     * Manually adjust the progress parameter.
     */
    public function setProgress(int|float $progress): ProgressBar
    {
        /** Increase maxProgress if progress unexpectedly gets higher than progress */
        if ($progress > $this->maxProgress) {
            $this->setMaxProgress($progress);
        }

        $this->progress = max(0, $progress);

        $this->setPercentage();

        return $this;
    }

    /**
     * Show cursor again after finishing progress bar
     */
    private function showCursor(): ProgressBar
    {
        echo "\x1b[?25h\x1b[?0c";

        return $this;
    }

    /**
     * Start displaying the progress bar.
     */
    public function start(): void
    {
        $this->startTime = time();

        $this->lastDisplayTime = microtime(true);

        $this->hideCursor()
            ->display();
    }

    /**
     * Advance progress in the progress bar.
     */
    public function tick(int $progress = 1): void
    {
        if ($progress == 0 && $this->maxProgressIsZero) {
            $this->finish();

            return;
        }

        $this->setProgress($this->progress + $progress)
            ->setEstimatedTime();

        /** Throttle refreshing of display for smoother animation  */
        if ((microtime(true) - $this->lastDisplayTime) < $this->drawFrequency) {
            return;
        }

        $this->lastDisplayTime = time();

        $this->display();
    }
}
