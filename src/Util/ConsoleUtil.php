<?php

namespace App\Util;

use Symfony\Component\Console\Helper\ProgressBar;

class ConsoleUtil
{
    public const PROGRESS_BAR_FILES_OPERATION_FORMAT = 'files_operation';
    public const PROGRESS_BAR_DONE_FORMAT = 'done';

    public static function defineFilesOperationProgressBarFormat(): void
    {
        ProgressBar::setFormatDefinition(
            self::PROGRESS_BAR_FILES_OPERATION_FORMAT,
            ' %current%/%max% -- %message% (%filename%)'
        );
    }

    public static function defineDoneProgressBarFormat(): void
    {
        ProgressBar::setFormatDefinition(self::PROGRESS_BAR_DONE_FORMAT, ' Done!');
    }
}