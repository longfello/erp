<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 05.08.2015
 */
namespace app\components;

use skeeks\yii2\assetsAuto\components\HtmlCompressor;
use yii\helpers\FileHelper;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Application;
use yii\web\JsExpression;
use yii\web\Response;
use yii\web\View;

/**
 * Class AssetsAutoCompressComponent
 * @package skeeks\yii2\assetsAuto
 */
class AssetsAutoCompressComponent extends \skeeks\yii2\assetsAuto\AssetsAutoCompressComponent
{
	public $enabled = false;

	/**
	 * Enable minification css in html code
	 * @var bool
	 */
	public $cssCompress = true;


	/**
	 * Turning association css files
	 * @var bool
	 */
	public $cssFileCompile = true;

	/**
	 * Trying to get css files to which the specified path as the remote file, skchat him to her.
	 * @var bool
	 */
	public $cssFileRemouteCompile = false;

	/**
	 * Enable compression and processing before being stored in the css file
	 * @var bool
	 */
	public $cssFileCompress = false;

	/**
	 * Moving down the page css files
	 * @var bool
	 */
	public $cssFileBottom = false;

	/**
	 * Transfer css file down the page and uploading them using js
	 * @var bool
	 */
	public $cssFileBottomLoadOnJs = false;

    /**
     * @param array $files
     * @return array
     */
    protected function _processingJsFiles($files = [])
    {
        $fileName   =  md5( implode(array_keys($files)) . $this->getSettingsHash()) . '.js';
        $publicUrl  = \Yii::getAlias('@web/data/assets/js-compress/' . $fileName);

        $rootDir    = basedir.'/data/assets/js-compress';
        $rootUrl    = $rootDir . '/' . $fileName;

        if (file_exists($rootUrl))
        {
            $resultFiles        = [];

            foreach ($files as $fileCode => $fileTag)
            {
                if (!Url::isRelative($fileCode))
                {
                    $resultFiles[$fileCode] = $fileTag;
                } else
                {
                    if ($this->jsFileRemouteCompile)
                    {
                        $resultFiles[$fileCode] = $fileTag;
                    }
                }
            }

            $publicUrl                  = $publicUrl . "?v=" . filemtime($rootUrl);
            $resultFiles[$publicUrl]    = Html::jsFile($publicUrl);
            return $resultFiles;
        }

        //Reading the contents of the files
        try
        {
            $resultContent  = [];
            $resultFiles    = [];
            foreach ($files as $fileCode => $fileTag)
            {
                if (Url::isRelative($fileCode))
                {
                    $contentFile = $this->fileGetContents( Url::to(\Yii::getAlias($fileCode), true) );
                    $resultContent[] = trim($contentFile) . "\n;";;
                } else
                {
                    if ($this->jsFileRemouteCompile)
                    {
                        //Пытаемся скачать удаленный файл
                        $contentFile = $this->fileGetContents( $fileCode );
                        $resultContent[] = trim($contentFile);
                    } else
                    {
                        $resultFiles[$fileCode] = $fileTag;
                    }
                }
            }
        } catch (\Exception $e)
        {
            \Yii::error($e->getMessage(), static::className());
            return $files;
        }

        if ($resultContent)
        {
            $content = implode($resultContent, ";\n");
            if (!is_dir($rootDir))
            {
                if (!FileHelper::createDirectory($rootDir, 0777))
                {
                    return $files;
                }
            }

            if ($this->jsFileCompress)
            {
                $content = \JShrink\Minifier::minify($content, ['flaggedComments' => $this->jsFileCompressFlaggedComments]);
            }

            $page = \Yii::$app->request->absoluteUrl;
            $useFunction = function_exists('curl_init') ? 'curl extension' : 'php file_get_contents';
            $filesString = implode(', ', array_keys($files));

            \Yii::info("Create js file: {$publicUrl} from files: {$filesString} to use {$useFunction} on page '{$page}'", static::className());

            $file = fopen($rootUrl, "w");
            fwrite($file, $content);
            fclose($file);
        }


        if (file_exists($rootUrl))
        {
            $publicUrl                  = $publicUrl . "?v=" . filemtime($rootUrl);
            $resultFiles[$publicUrl]    = Html::jsFile($publicUrl);
            return $resultFiles;
        } else
        {
            return $files;
        }
    }

    /**
     * @param array $files
     * @return array
     */
    protected function _processingCssFiles($files = [])
    {
        $fileName   =  md5( implode(array_keys($files)) . $this->getSettingsHash() ) . '.css';
        $publicUrl  = \Yii::getAlias('@web/data/assets/css-compress/' . $fileName);

        $rootDir    = basedir.'/data/assets/css-compress';
        $rootUrl    = $rootDir . '/' . $fileName;

        if (file_exists($rootUrl))
        {
            $resultFiles        = [];

            foreach ($files as $fileCode => $fileTag)
            {
                if (Url::isRelative($fileCode))
                {

                } else
                {
                    if (!$this->cssFileRemouteCompile)
                    {
                        $resultFiles[$fileCode] = $fileTag;
                    }
                }

            }

            $publicUrl                  = $publicUrl . "?v=" . filemtime($rootUrl);
            $resultFiles[$publicUrl]    = Html::cssFile($publicUrl);
            return $resultFiles;
        }

        //Reading the contents of the files
        try
        {
            $resultContent  = [];
            $resultFiles    = [];
            foreach ($files as $fileCode => $fileTag)
            {
                if (Url::isRelative($fileCode))
                {
                    $contentTmp         = trim($this->fileGetContents( Url::to(\Yii::getAlias($fileCode), true) ));

                    $fileCodeTmp = explode("/", $fileCode);
                    unset($fileCodeTmp[count($fileCodeTmp) - 1]);
                    $prependRelativePath = implode("/", $fileCodeTmp) . "/";

                    $contentTmp    = \Minify_CSS::minify($contentTmp, [
                        "prependRelativePath" => $prependRelativePath,

                        'compress'          => true,
                        'removeCharsets'    => true,
                        'preserveComments'  => true,
                    ]);

                    //$contentTmp = \CssMin::minify($contentTmp);

                    $resultContent[] = $contentTmp;
                } else
                {
                    if ($this->cssFileRemouteCompile)
                    {
                        //Пытаемся скачать удаленный файл
                        $resultContent[] = trim($this->fileGetContents( $fileCode ));
                    } else
                    {
                        $resultFiles[$fileCode] = $fileTag;
                    }
                }
            }
        } catch (\Exception $e)
        {
            \Yii::error($e->getMessage(), static::className());
            return $files;
        }

        if ($resultContent)
        {
            $content = implode($resultContent, "\n");
            if (!is_dir($rootDir))
            {
                if (!FileHelper::createDirectory($rootDir, 0777))
                {
                    return $files;
                }
            }

            if ($this->cssFileCompress)
            {
                $content = \CssMin::minify($content);
            }

            $page = \Yii::$app->request->absoluteUrl;
            $useFunction = function_exists('curl_init') ? 'curl extension' : 'php file_get_contents';
            $filesString = implode(', ', array_keys($files));

            \Yii::info("Create css file: {$publicUrl} from files: {$filesString} to use {$useFunction} on page '{$page}'", static::className());


            $file = fopen($rootUrl, "w");
            fwrite($file, $content);
            fclose($file);
        }


        if (file_exists($rootUrl))
        {
            $publicUrl                  = $publicUrl . "?v=" . filemtime($rootUrl);
            $resultFiles[$publicUrl]    = Html::cssFile($publicUrl);
            return $resultFiles;
        } else
        {
            return $files;
        }
    }
}