<?php
/**
 * @author
 * @license MIT
 */
namespace fate\model\validators;

/**
 * 校验字符串
 *
 * class XxxModel extends Model {
 *      rules() {
 *          return [
 *              [
 *                  'rule' => [
 *                      'classPath' => 'fate\model\validators\StringValidator',
 *                      'minLength' => 1,
 *                      'maxLength' => 2333
 *                  ],
 *                  'attributes' => ['name'],
 *                  'messages' => ['length of the name should be between 1 and 2333']
 *              ]
 *          ];
 *      }
 * }
 *
 */
class StringValidator extends \fate\model\Validator {

    /**
     * @var string 编码
     */
    public $encoding = 'UTF-8';

    /**
     * @var boolean trim space
     */
    public $trim = true;

    /**
     * @var number 最小长度
     */
    public $minLength = 1;

    /**
     * @var number 最大长度
     */
    public $maxLength = 2333;

    /**
     * {@inheritdoc}
     */
    public function validate($attributeName, $attributeValue) {
        $info = $this->getMessage($attributeName);

        if(null === $attributeValue) {
            $attributeValue = '';
        }

        if('' !== $attributeValue && $this->trim) {
            $attributeValue = trim($attributeValue);
        }

        $length = mb_strlen($attributeValue, $this->encoding);

        if($length < $this->minLength || $length > $this->maxLength) {
            return '' === $info
                ? 'length of the '. $attributeName .' should be between '. $this->minLength .' and '. $this->maxLength
                : $info;
        }

        return '';
    }

}
