<?php
/**
 * @author
 * @license MIT
 */
namespace fate\model\validators;

/**
 * 检查一个属性是不是空值 null or 空字符串
 *
 * class XxxModel extends Model {
 *      rules() {
 *          return [
 *              [
 *                  'rule' => 'fate\model\validators\RequiredValidator',
 *                  'attributes' => ['name', 'email'],
 *                  'messages' => ['name is required', 'email is required']
 *              ]
 *          ];
 *      }
 * }
 *
 */
class RequiredValidator extends \fate\model\Validator {

    /**
     * {@inheritdoc}
     */
    public function validate($attributeName, $attributeValue) {
        $info = $this->getMessage($attributeName);

        if(null === $attributeValue || '' === $attributeValue) {
            return '' === $info ? $attributeName . ' is required' : $info;
        }

        return '';
    }

}
