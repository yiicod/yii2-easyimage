<?php

namespace yiicod\easyimage\base;

use Imagine\Image\ManipulatorInterface;

/**
 * Interface ToolInterface
 * Base interface for easyimage tools
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 *
 * @package yiicod\easyimage\base
 */
interface ToolInterface
{
    /**
     * Handle image
     *
     * @param ManipulatorInterface $image
     * @param array $params
     *
     * @return ManipulatorInterface
     */
    public static function handle(ManipulatorInterface $image, array $params = []): ManipulatorInterface;
}
