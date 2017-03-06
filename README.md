Easyimage
=================================
You don't need to create many types of thumbnails for images in your project.
You can create a thumbnail directly in the `View`. Thumbnail will automatically cached. It's easy and powerful!
  
Features:
- Easy to use
- Support `Imagick`, `Gmagick` and `GD2`
- Automatically images caching
- Cache sorting to subdirectories

###Installing and configuring
Extract the `EasyImage` folder under `vendor\yiicod`

Add the following to your config file `components` section:

```php
'easyimage' => [
    'class' => 'yiicod\easyimage\EasyImage',
    'webrootAlias' => '@frontend/web',
    'cachePath' => '/uploads/easyimage/',
    'imageOptions' => [
        'quality' => 100
    ],
],
```
####Parameters
- string `webrootAlias` webroot folder path
- string `cachePath` cache directory path in webroot folder
- array `imageOptions` array with default output image options which will used to save image

##Usage
```php
Yii::$app->easyimage->getUrl($file, $params = [], $absolute = false); //Get cached image url
Yii::$app->easyimage->getPath($file, $params = []); //Get cached image path
```

####Parameters
- string `$file` required - Image file path
- array `$params` image tools params Default: []
- mixed `$absolute` get absolute (true) url or not (false). Will use in Url::to() helper method

###Available tools and parameters
```php
'crop' => [
    'width' => 200, //Required
    'height' => 200, //Required
    'offset_x' => 100,
    'offset_y' => 100,
],
'flip' => [
    'axis' => 'vertical', //Required, available 'vertical' or 'horizontal'
],
'resize' => [
    'width' => 200, //Required
    'height' => 200, //Required
],
'scale' => [
    'width' => 200, //Required
    'height' => 200, //Required
],
'rotate' => [
    'angle' => 45 //Required
],
'thumbnail' => [
    'width' => 100, //Required one of dimensions
    'height' => 100, //Required one of dimensions
],
'watermark' => [
    'image' => $file, //Required
    'offset_x' => 100,
    'offset_y' => 100,
],
'background' => [
    'color' => '#000', //Required
    'alpha' => 0 //Background color's alpha
],
```

###Tools
You can add additional tools in config:
```php
'easyimage' => [
    'class' => 'yiicod\easyimage\EasyImage',
    'webrootAlias' => '@frontend/web',
    'cachePath' => '/uploads/easyimage/',
    'tools' => [
        'crop' => Crop::class,
    ],
],
```
- 'crop' - tool name which will use in getUrl() or getPath() params
- Crop::class - tool class name which must implement yiicod\easyimage\base\ToolInterface
