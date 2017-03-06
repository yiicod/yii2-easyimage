<?php

namespace yiicod\easyimage\tools;

use Exception;
use Imagine\Image\Color;
use Imagine\Image\ManipulatorInterface;
use yiicod\easyimage\base\ToolInterface;

/**
 * Class Rotate
 * Rotate image tool
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 * @package yiicod\easyimage\tools
 */
class Rotate implements ToolInterface
{
    /**
     * Handle image
     *
     * @param ManipulatorInterface $image
     * @param array $params
     *
     * @return ManipulatorInterface
     * @throws Exception
     */
    public static function handle(ManipulatorInterface $image, array $params = []) : ManipulatorInterface
    {
        if (false === isset($params['angle'])) {
            throw new Exception('Param "angle" is required for action "Rotate"');
        }

        return $image->rotate(
            $params['angle'],
            isset($params['background']) ? new Color($params['background']) : null
        );
    }
}