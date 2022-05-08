<?php
/**
 * @author
 * @license MIT
 */
namespace fate\model;

use Fate;
use fate\core\ModelException;

/**
 * 用于存储和校验与数据库相关的数据
 */
class Model extends fate\core\Component {

    /**
     * @var string 模型名
     */
    public $modelName = '';

    /**
     * @var array 数据字段配置 一般与数据库字段一致
     *
     * [
     *      'name' => 'defaultValue',
     *      'age' => defaultValue
     * ]
     *
     */
    public $attributes = null;

    /**
     * @var array 模型属性与表单字段对应关系 用于解决模型字段与表单字段名称不同问题
     *
     * [
     *      'name' => 'form_user_name'
     * ]
     *
     */
    public $attributesMap = null;

    /**
     * @var string[] 错误信息
     */
    public $messages = [];

    /**
     * Returns the validation rules for attributes
     *
     * [
     *      [
     *          // 必选参数
     *          'rule' => 'fate\model\RequiredValidator',
     *          'attributes' => ['name', 'age'],
     *          // 可选参数 错误信息
     *          'messages' => ['name is required', 'age is required']
     *      ]
     * ]
     *
     */
    public function rules() {
        return null;
    }

    /**
     * 获取所有属性
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * 获取某个属性
     *
     * @param string $attribute 属性名
     * @throws ModelException
     */
    public function getAttribute($attribute) {
        if(null === $this->attributes) {
            throw new ModelException('The model has no attribute to get');
        }

        return $this->attributes[$attribute];
    }

    /**
     * 设置属性
     *
     * @param array $attributes 属性
     */
    public function setAttributes($attributes) {
        $this->attributes = $attributes;
    }

    /**
     * 设置一个属性
     *
     * @param string $attribute 属性名
     * @param mixed $value 属性值
     */
    public function setAttribute($attribute, $value) {
        if(null === $this->attributes) {
            $this->attributes = [];
        }

        $this->attributes[$attribute] = $value;
    }

    /**
     * 获取验证器
     *
     * @return Validator[]
     */
    public function getValidators() {
        $rules = $this->rules();
        if(null === $rules) {
            return null;
        }

        $ret = [];

        foreach($rules as $item) {
            $messages = isset($item['messages']) ? $item['messages'] : null;

            // validator object
            if($item['rule'] instanceof Validator) {
                $item['rule']->model = $this;
                $item['rule']->attributes = $item['attributes'];
                $item['rule']->messages = $messages;

                $ret[] = $item['rule'];
                continue;
            }

            // string
            if(is_string($item['rule'])) {
                $ret[] = Fate::createObject([
                    'classPath' => $item['rule'],
                    'model' => $this,
                    'attributes' => $item['attributes'],
                    'messages' => $messages
                ]);

                continue;
            }

            // config
            $ret[] = Fate::createObject(
                array_merge([
                    'model' => $this,
                    'attributes' => $item['attributes'],
                    'messages' => $messages
                ], $item['rule'])
            );
        }

        return $ret;
    }

    /**
     * 填充模型
     *
     * @return boolean
     */
    public function fill(& $data) {
        if(null === $this->attributes) {
            throw new ModelException('The model has no attributes to fill');
        }

        if(empty($data)) {
            return false;
        }

        $fields = array_keys($this->attributes);
        $value = '';
        foreach($fields as $field) {
            if( null !== $this->attributesMap && isset($this->attributesMap[$field]) ) {
                $value = $data[ $this->attributesMap[$field] ];
            } else {
                $value = $data[ $field ];
            }

            $this->attributes[$field] = $value;
        }

        return true;
    }

    /**
     * 执行验证
     *
     * @return boolean
     */
    public function validate() {
        if(null === $this->attributes) {
            throw new ModelException('The model has no attributes to validate');
        }

        $validators = $this->getValidators();
        if(null === $validators) {
            return true;
        }

        foreach($validators as $validator) {
            $this->messages = array_merge($this->messages, $validator->validateAttributes());
        }

        return count($this->messages) === 0;
    }

    /**
     * 获取错误信息
     *
     * @return string[]
     */
    public function getErrors() {
        return $this->messages;
    }

    /**
     * 获取第一个错误信息 如果没有则返回空
     *
     * @return string
     */
    public function getFirstError() {
        if(count($this->messages) > 0) {
            return $this->messages[0];
        }

        return '';
    }

    /**
     * 清空错误信息
     */
    public function clearErrors() {
        $this->messages = [];
    }

}
