<?php

namespace yiicod\easyimage\tools;

use Exception;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use yiicod\easyimage\base\ToolInterface;

/**
 * Class Resize
 * Resize image tool
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 * @package yiicod\easyimage\tools
 */
class Resize implements ToolInterface
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
    public static function handle(ManipulatorInterface $image, array $params = []): ManipulatorInterface
    {
        if (false === isset($params['width']) || false === isset($params['height'])) {
            throw new Exception('Params "width" and "height" is required for action "Resize"');
        }

        return $image->resize(
            new Box($params['width'], $params['height']),
            isset($params['filter']) ? $params['filter'] : ImageInterface::FILTER_UNDEFINED
        );
    }
}