<?php
/**
 * @author
 * @license MIT
 */
namespace fate\model\validators;

/**
 * Check if the attribute value is a boolean value
 *
 * class XxxModel extends Model {
 *      rules() {
 *          return [
 *              [
 *                  'rule' => 'fate\model\validators\BooleanValidator',
 *                  'attributes' => ['booleanAttr'],
 *                  'messages' => ['booleanAttr is invalid']
 *              ]
 *          ];
 *      }
 * }
 *
 */
class BooleanValidator extends \fate\model\Validator {
    /**
     * @var boolean 是否严格模式
     */
    public $strict = true;

    /**
     * {@inheritdoc}
     */
    public function validate($attributeName, $attributeValue) {
        $valid = false;
        $info = $this->getMessage($attributeName);

        if($this->strict) {
            $valid = true === $attributeValue || false === $attributeValue;

        } else {
            $valid = true == $attributeValue || false == $attributeValue;
        }

        if(!$valid) {
            return '' === $info ? $attributeName . ' is invalid' : $info;
        }

        return '';
    }
}
