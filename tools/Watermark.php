<?php

namespace yiicod\easyimage\tools;

use Exception;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use yiicod\easyimage\base\ToolInterface;
use yiicod\easyimage\EasyImage;

/**
 * Class Watermark
 * Watermark image tool
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 *
 * @package yiicod\easyimage\tools
 */
class Watermark implements ToolInterface
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
        if (false === isset($params['image'])) {
            throw new Exception('Param "image" is required for action "Watermark"');
        }

        $watermark = EasyImage::getImagine()->open($params['image'])->copy();

        return $image->paste($watermark, new Point(
            isset($params['offset_x']) ? $params['offset_x'] : 0,
            isset($params['offset_y']) ? $params['offset_y'] : 0
        ));
    }
}
