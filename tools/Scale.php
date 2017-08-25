<?php

namespace yiicod\easyimage\tools;

use Exception;
use Imagine\Image\ManipulatorInterface;
use yiicod\easyimage\base\ToolInterface;

/**
 * Class Scale
 * Scale image tool
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 *
 * @package yiicod\easyimage\tools
 */
class Scale implements ToolInterface
{
    /**
     * Handle image
     *
     * @param ManipulatorInterface $image
     * @param array $params
     *
     * @return ManipulatorInterface
     *
     * @throws Exception
     */
    public static function handle(ManipulatorInterface $image, array $params = []): ManipulatorInterface
    {
        if (false === isset($params['width']) && false === isset($params['height'])) {
            throw new Exception('Params "width" or "height" is required for action "Scale"');
        }

        $image->getImagick()->scaleImage($params['width'] ?? 0, $params['height'] ?? 0);

        return $image;
    }
}
