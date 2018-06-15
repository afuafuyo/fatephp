<?php
/**
 * @author
 * @license MIT
 */
namespace y\helpers;

use y\core\Exception;

class FileHelper {
    
    /**
     * 创建文件夹
     *
     * @param string $path 目录路径
     * @param integer $mode 目录权限
     * @param boolean $recursive 递归创建目录
     * @return boolean 目录是否创建成功
     * @throws \y\core\Exception
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true) {
        if(is_dir($path)) {
            return true;
        }
        
        $parentDir = dirname($path);
        if($recursive && !is_dir($parentDir)) {
            static::createDirectory($parentDir, $mode, true);
        }
        
        try {
            $result = mkdir($path, $mode);
            chmod($path, $mode);
            
        } catch(\Exception $e) {
            throw new Exception("Failed to create directory '$path'", $e->getCode(), $e);
        }
        
        return $result;
    }
    
    /**
     * 删除目录
     *
     * @param string $dir 要删除的目录
     */
    public static function removeDirectory($dir) {
        if(!is_dir($dir)) {
            return;
        }
        
        if(!is_link($dir)) {
            if(!($handle = opendir($dir))) {
                return;
            }
            while(($file = readdir($handle)) !== false) {
                if($file === '.' || $file === '..') {
                    continue;
                }
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if(is_dir($path)) {
                    static::removeDirectory($path);
                } else {
                    unlink($path);
                }
            }
            closedir($handle);
        }
        
        if(is_link($dir)) {
            unlink($dir);
            
        } else {
            rmdir($dir);
        }
    }
    
}
