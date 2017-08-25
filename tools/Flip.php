<?php

namespace yiicod\easyimage\tools;

use Exception;
use Imagine\Image\ManipulatorInterface;
use yiicod\easyimage\base\ToolInterface;

/**
 * Class Flip
 * Flip image tool
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 *
 * @package yiicod\easyimage\tools
 */
class Flip implements ToolInterface
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
        if (false === isset($params['axis'])) {
            throw new Exception('Param "axis" is required for action "Flip"');
        }

        switch ($params['axis']) {
            case 'horizontal':
                return $image->flipHorizontally();
                break;
            case 'vertical':
                return $image->flipVertically();
                break;
            default:
                throw new Exception('Param "axis" must be equal "horizontal" or "vertical" for action "Rotate"');
                break;
        }
    }
}
