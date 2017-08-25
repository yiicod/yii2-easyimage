<?php

namespace yiicod\easyimage\tools;

use Exception;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use yiicod\easyimage\base\ToolInterface;
use yiicod\easyimage\EasyImage;

/**
 * Class Background
 * Background image color tool
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 *
 * @package yiicod\easyimage\tools
 */
class Background implements ToolInterface
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
        if (false === isset($params['color'])) {
            throw new Exception('Param "color" is required for action "Background"');
        }

        $size = $image->getSize();

        $box = new Box($size->getWidth(), $size->getHeight());
        $thumb = EasyImage::getImagine()->create($box, new Color(
            $params['color'],
            $params['alpha'] ?? 0
        ));

        $thumb->paste($image, new Point(0, 0));

        return $thumb;
    }
}
