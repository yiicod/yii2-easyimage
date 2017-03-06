<?php

namespace yiicod\easyimage\tools;

use Exception;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use yiicod\easyimage\base\ToolInterface;

/**
 * Class Thumbnail
 * Thumbnail image tool
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 * @package yiicod\easyimage\tools
 */
class Thumbnail implements ToolInterface
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
        if (false === isset($params['width']) && false === isset($params['height'])) {
            throw new Exception('Params "width" or "height" is required for action "Thumbnail"');
        }

        /** @var BoxInterface $size */
        $size = $image->getSize();

        if (isset($params['width'], $params['height'])) {
            $newSize = $size->widen($params['width']);

            if ($newSize->getHeight() < $params['height']) {
                $newSize = $newSize->heighten($params['height']);
            }

            $image->resize($newSize);
            $result = $image->crop(new Point(
                round(($newSize->getWidth() - $params['width']) / 2),
                round(($newSize->getHeight() - $params['height']) / 2)
            ), new Box($params['width'], $params['height']));
        } elseif (isset($params['width'])) {
            $result = $image->resize($size->widen($params['width']));
        } elseif (isset($params['height'])) {
            $result = $image->resize($size->heighten($params['height']));
        }

        return $result;
    }
}
