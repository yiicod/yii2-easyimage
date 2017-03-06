<?php

namespace yiicod\easyimage;

use Exception;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yiicod\easyimage\base\ToolInterface;
use yiicod\easyimage\tools\Background;
use yiicod\easyimage\tools\Crop;
use yiicod\easyimage\tools\Flip;
use yiicod\easyimage\tools\Resize;
use yiicod\easyimage\tools\Rotate;
use yiicod\easyimage\tools\Scale;
use yiicod\easyimage\tools\Thumbnail;
use yiicod\easyimage\tools\Watermark;

/**
 * Class EasyImage
 * Easy image extension
 *
 * @author Virchenko Maksim <muslim1992@gmail.com>
 * @package yiicod\easyimage
 */
class EasyImage extends Component
{
    /**
     * GD2 driver definition for Imagine implementation using the GD library.
     */
    const DRIVER_GD2 = 'gd2';
    /**
     * imagick driver definition.
     */
    const DRIVER_IMAGICK = 'imagick';
    /**
     * gmagick driver definition.
     */
    const DRIVER_GMAGICK = 'gmagick';
    /**
     * @var string alias for webroot folder
     */
    public $webrootAlias = '@webroot';
    /**
     * @var string relative path where the cache files are kept
     */
    public $cachePath = '/assets/easyimage/';
    /**
     * @var int cache lifetime in seconds
     */
    public $cacheTime = 2592000;
    /**
     * @var array output image options
     */
    public $imageOptions = [
        'quality' => 100,
    ];
    /**
     * Additional tools to work with images
     * ['toolName' => 'toolClass']
     * Tool class must implement yiicod\easyimage\base\ToolInterface
     *
     * @var array
     */
    public $tools = [];
    /**
     * Default empty image in "base64" string
     *
     * @var string
     */
    public $emptyImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=';
    /**
     * @var array|string the driver to use. This can be either a single driver name or an array of driver names.
     * If the latter, the first available driver will be used.
     */
    public static $driver = [self::DRIVER_IMAGICK, self::DRIVER_GMAGICK, self::DRIVER_GD2];
    /**
     * Imagine instance
     *
     * @var ImagineInterface
     */
    protected static $imagine;

    /**
     * Init component
     */
    public function init()
    {
        $this->initTools();

        Yii::setAlias('yiicod', realpath(dirname(__FILE__) . '/..'));
    }

    /**
     * Initialize default tools
     */
    protected function initTools()
    {
        $this->tools = ArrayHelper::merge([
            'crop' => Crop::class,
            'flip' => Flip::class,
            'resize' => Resize::class,
            'rotate' => Rotate::class,
            'scale' => Scale::class,
            'watermark' => Watermark::class,
            'thumbnail' => Thumbnail::class,
            'background' => Background::class,
        ], $this->tools);
    }

    /**
     * Returns the `Imagine` object that supports various image manipulations.
     * @return ImagineInterface the `Imagine` object
     */
    public static function getImagine(): ImagineInterface
    {
        if (self::$imagine === null) {
            self::$imagine = static::createImagine();
        }

        return self::$imagine;
    }

    /**
     * @param ImagineInterface $imagine the `Imagine` object.
     */
    public static function setImagine(ImagineInterface $imagine)
    {
        self::$imagine = $imagine;
    }

    /**
     * Creates an `Imagine` object based on the specified [[driver]].
     * @return ImagineInterface the new `Imagine` object
     * @throws Exception
     */
    protected static function createImagine(): ImagineInterface
    {
        foreach ((array)static::$driver as $driver) {
            switch ($driver) {
                case self::DRIVER_IMAGICK:
                    if (class_exists('Imagick', false)) {
                        return new \Imagine\Imagick\Imagine();
                    }
                    break;
                case self::DRIVER_GMAGICK:
                    if (class_exists('Gmagick', false)) {
                        return new \Imagine\Gmagick\Imagine();
                    }
                    break;
                case self::DRIVER_GD2:
                    if (function_exists('gd_info')) {
                        return new \Imagine\Gd\Imagine();
                    }
                    break;
                default:
                    throw new Exception("Unknown driver: $driver");
            }
        }
        throw new Exception("Your system does not support any of these drivers: " . implode(',', (array)static::$driver));
    }

    /**
     * Get url for cached image using available tools
     *
     * @param string $file
     * @param array $params
     * @param bool $absolute
     *
     * @return string
     */
    public function getUrl(string $file, array $params = [], $absolute = false): string
    {
        $cacheFilePath = $this->getCacheFile($file, $params);

        if ($cacheFilePath) {
            return $this->createUrl($cacheFilePath, $absolute);
        }

        return $this->getEmptyImage();
    }

    /**
     * Get path for cached image using available tools
     *
     * @param string $file
     * @param array $params
     *
     * @return string
     */
    public function getPath(string $file, array $params = []): string
    {
        $cacheFilePath = $this->getCacheFile($file, $params);

        if ($cacheFilePath) {
            return $this->createPath($cacheFilePath);
        }

        return $this->getEmptyImage();
    }

    /**
     * Get component instance
     * Added for better IDE support
     *
     * @return mixed
     */
    public static function getInstance(): EasyImage
    {
        return Yii::$app->easyimage;
    }

    /**
     * Get cahced image
     *
     * @param $file
     * @param array $params
     *
     * @return bool|string
     */
    protected function getCacheFile(string $file, array $params = [])
    {
        try {
            $cacheFilePath = $this->generateCacheFilePath($file, $params);
            $filePath = $this->createPath($cacheFilePath);

            if (false === file_exists($filePath) ||
                (time() - filemtime($filePath) > $this->cacheTime) ||
                filemtime($file) > filemtime($filePath)
            ) {
                $this->makeDir(substr($filePath, 0, strpos($filePath, basename($filePath))));
                $this->createImage($file, $params, $filePath);
            }

            return $cacheFilePath;
        } catch (Exception $e) {
            $this->log($e);

            return false;
        }
    }

    /**
     * Create url for cached image
     *
     * @param string $filePath
     * @param bool $absolute
     *
     * @return string
     */
    protected function createUrl(string $filePath, $absolute = false): string
    {
        return rtrim(Url::base($absolute), DIRECTORY_SEPARATOR) . $filePath;
    }

    /**
     * Create path for cached image
     *
     * @param string $filePath
     *
     * @return string
     */
    protected function createPath(string $filePath): string
    {
        return Yii::getAlias($this->webrootAlias) . $filePath;
    }

    /**
     * Generate cached image path
     *
     * @param string $file
     * @param array $params
     *
     * @return string
     */
    protected function generateCacheFilePath(string $file, array $params = []): string
    {
        $hash = md5($file . serialize($params));

        $cacheFolderPath = $this->cachePath . $hash{0};

        $cacheFileExt = isset($params['type']) ? $params['type'] : pathinfo($file, PATHINFO_EXTENSION);
        $cacheFileName = $hash . '.' . $cacheFileExt;

        $cacheFilePath = $cacheFolderPath . DIRECTORY_SEPARATOR . $cacheFileName;

        return $cacheFilePath;
    }

    /**
     * Create new image using available tools
     *
     * @param string $file
     * @param array $params
     * @param string $savePath
     *
     * @return \Imagine\Image\ManipulatorInterface
     */
    protected function createImage(string $file, array $params, string $savePath): ManipulatorInterface
    {
        $image = self::getImagine()->open($file)->copy();
        $options = $this->imageOptions;

        foreach ($params as $key => $value) {
            if (isset($this->tools[$key]) && in_array(ToolInterface::class, class_implements($this->tools[$key]))) {
                $toolClass = $this->tools[$key];
                $image = $toolClass::handle($image, $value);
            } else {
                $options[$key] = $value;
            }
        }

        return $image->save($savePath, $params);
    }

    /**
     * Make directory to save cache image
     *
     * @param string $savePath
     */
    protected function makeDir(string $savePath)
    {
        if (!file_exists($savePath)) {
            mkdir($savePath, 0755, true);
        }
    }

    /**
     * Get default empty image
     *
     * @return string
     */
    protected function getEmptyImage(): string
    {
        return $this->emptyImage;
    }

    /**
     * Log exception
     *
     * @param Exception $e
     */
    protected function log(Exception $e)
    {
        Yii::error(sprintf("%s (%s : %s)\nStack trace:\n%s", $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()), 'easyimage');
    }
}