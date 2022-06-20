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

    /** Character to display remaining progress in progress bar */
    private string $emptyBarCharacter = '.';

    /** Estimated time to finish in seconds */
    private int $estimatedTime = 0;

    /** Current progress in percentage */
    private int $percentage = 0;

    /** Time when progress bar starts displaying  */
    private int $startTime;

    /**
     * Initialize and set progress and maxProgress when available.
     * Use separate setProgress() and setMaxProgress() methods when undetermined during initialization.
     *
     * @param int $progress Current progress
     * @param int $maxProgress Maximum progress
     */
    public function __construct(
        private int $progress = 0,
        private int $maxProgress = 100,
    )
    {
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

        echo sprintf('%s%s/%d [%s%s] %s%%',
            "\r",
            str_pad(string: $this->progress, length: strlen((string) $this->maxProgress), pad_type: STR_PAD_LEFT),
            $this->maxProgress,
            str_repeat($this->barCharacter, $barCompleteWidth),
            str_repeat($this->emptyBarCharacter, $barIncompleteWidth),
            str_pad(string: $this->percentage, length: 3, pad_type: STR_PAD_LEFT)
        );

        /** Remove estimated time from display when done */
        if ($this->percentage < 100) {
            echo sprintf(' (%s)', (
                $this->estimatedTime < 86400 ? gmdate('H:i:s', $this->estimatedTime) : '>23:59:59')
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
        return $this->maxProgress;
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
        return $this->progress;
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
    public function iterate(iterable $iterable): iterable
    {
        $this->setMaxProgress(is_countable($iterable) ? count($iterable): 0);

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
       $this->estimatedTime = round((
           time() - $this->startTime) / $this->progress * ($this->maxProgress - $this->progress)
       );

       return $this;
    }

    /**
     * Manually adjust the maxProgress parameter when undetermined or changed.
     */
    public function setMaxProgress(int $maxProgress): ProgressBar
    {
        $this->maxProgress = max(1, $maxProgress);

        return $this;
    }

    /**
     * Calculate completion in percentage
     */
    private function setPercentage(): void
    {
        $this->percentage = number_format(($this->progress / $this->maxProgress) * 100, 2);
    }

    /**
     * Manually adjust the progress parameter.
     */
    public function setProgress(int $progress): ProgressBar
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
     * Start displaying the progress bar.
     */
    public function start(): void
    {
        $this->startTime = time();

        $this->display();
    }

    /**
     * Advance progress in the progress bar.
     */
    public function tick(int $progress = 1): void
    {
        $this->setProgress($this->progress + $progress)
            ->setEstimatedTime()
            ->display();
    }
}
