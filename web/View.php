<?php
/**
 * @author
 * @license MIT
 */
namespace fate\web;

use Fate;
use fate\core\FileNotFoundException;

/**
 * 视图类
 */
class View extends \fate\core\View {

    /**
     * @property boolean 是否开启布局视图
     */
    public $enableLayout = false;

    /**
     * @property string 布局文件路径
     */
    public $layout = 'app/views/layout';

    /**
     * @property string 页面标题
     */
    public $title = '';

    /**
     * @property string 页面描述
     */
    public $description = '';

    /**
     * @property string 内容 html
     */
    public $contentHtml = '';

    /**
     * @property array Head 部分资源
     */
    public $headAssets = null;

    /**
     * @property array Footer 部分资源
     */
    public $footerAssets = null;

    /**
     * 获取 head 部分资源
     *
     * @return string
     */
    public function getHeadAssets() {
        return null === $this->headAssets ? '' : implode("\n", $this->headAssets);
    }

    /**
     * 添加 head 部分资源
     *
     * @param string $asset 资源
     */
    public function addHeadAsset($asset) {
        if(null === $this->headAssets) {
            $this->headAssets = [];
        }

        $this->headAssets[] = $asset;
    }

    /**
     * 获取 footer 部分资源
     *
     * @return string
     */
    public function getFooterAssets() {
        return null === $this->footerAssets ? '' : implode("\n", $this->footerAssets);
    }

    /**
     * 添加 footer 部分资源
     *
     * @param string $asset 资源
     */
    public function addFooterAsset($asset) {
        if(null === $this->footerAssets) {
            $this->footerAssets = [];
        }

        $this->footerAssets[] = $asset;
    }

    /**
     * {@inheritdoc}
     */
    public function renderFile($file, $parameters) {
        if(!is_file($file)) {
            throw new FileNotFoundException('The view file not found: ' . $file);
        }

        // view content
        ob_start();
        ob_implicit_flush(false);
        extract($parameters, EXTR_OVERWRITE);
        include($file);
        $this->contentHtml = ob_get_clean();

        // layout content
        if($this->enableLayout) {
            $layoutFile = Fate::getPathAlias($this->layout);

            ob_start();
            ob_implicit_flush(false);
            include($layoutFile);
            $this->contentHtml = ob_get_clean();
        }

        return $this->contentHtml;
    }

}
