<?php
/**
 * @author
 * @license MIT
 */
namespace fate\model;

class Validator {

    /**
     * @var Model 所属模型
     */
    public $model = null;

    /**
     * @var string[] 待验证的属性
     *
     * ['name', 'age']
     *
     */
    public $attributes = null;

    /**
     * @var string[] 属性验证不通过时的错误信息 与 attributes 一一对应
     *
     * ['name is required', 'age is required']
     *
     */
    public $messages = null;

    /**
     * @var boolean 是否跳过校验
     */
    public $skip = false;

    /**
     * 执行验证
     *
     * @return string[]
     */
    public function validateAttributes() {
        $list = $this->attributes;
        $infos = [];

        for($i=0, $result=''; $i<count($list); $i++) {
            // 跳过检查
            if($this->skip) {
                // continue or break in experimental stage
                break;
            }

            $result = $this->validate($list[$i], $this->model->attributes[ $list[$i] ]);

            if('' !== $result) {
                $infos[] = $result;
            }
        }

        return $infos;
    }

    /**
     * 获取属性的错误描述
     *
     * @param string $attributeName 属性名
     * @return string 有配置错误信息则返回 否则返回空
     */
    public function getMessage($attributeName) {
        if(null === $this->messages) {
            return '';
        }

        $index = array_search($this->attributes, $attributeName);
        if(false === $index || count($this->messages) <= $index) {
            return '';
        }

        return $this->messages[$index];
    }

    /**
     * 验证一个属性 并返回错误信息
     *
     * @param string $attributeName 属性名
     * @param mixed $attributeValue 属性值
     * @return string 有错误时返回错误信息 无错误时返回空字符串
     */
    public function validate($attributeName, $attributeValue) {
        return '';
    }

}
