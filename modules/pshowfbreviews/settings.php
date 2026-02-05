<?php

/**
 * Class Logger
 *
 * @package \Monolog
 */
class Logger
{
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;
}

return [
    [
        'type' => 'radio',
        'label' => 'Logging level',
        'name' => 'LOGGING_LEVEL',
        'hint' => 'In production you should use "Warning" or higher.',
        'values' => [
            [
                'id' => 'debug',
                'value' => Logger::DEBUG,
                'label' => 'Debug',
            ],
            [
                'id' => 'debug',
                'value' => Logger::INFO,
                'label' => 'Info',
            ],
            [
                'id' => 'debug',
                'value' => Logger::NOTICE,
                'label' => 'Notice',
            ],
            [
                'id' => 'debug',
                'value' => Logger::WARNING,
                'label' => 'Warning',
            ],
            [
                'id' => 'debug',
                'value' => Logger::ERROR,
                'label' => 'Error',
            ],
            [
                'id' => 'debug',
                'value' => Logger::CRITICAL,
                'label' => 'Critical',
            ],
        ],
        'default' => Logger::WARNING,
    ],
];
