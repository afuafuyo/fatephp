<?php
/**
 * @author
 * @license MIT
 */
namespace fate\model\validators;

/**
 * 检查邮件地址是否合法
 *
 * class XxxModel extends Model {
 *      rules() {
 *          return [
 *              [
 *                  'rule' => 'fate\model\validators\EmailValidator',
 *                  'attributes' => ['user_email'],
 *                  'messages' => ['user email is invalid']
 *              ]
 *          ];
 *      }
 * }
 *
 */
class EmailValidator extends \fate\model\Validator {

    /**
     * 模式
     */
    public $pattern = '/^[a-zA-Z0-9_\.\-]+\@(?:[a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,8}$/';

    /**
     * {@inheritdoc}
     */
    public function validate($attributeName, $attributeValue) {
        $info = $this->getMessage($attributeName);

        if(null === $attributeValue || !preg_match($this->pattern, $attributeValue)) {
            return '' === $info ? $attributeName . ' is invalid' : $info;
        }

        return '';
    }

}
