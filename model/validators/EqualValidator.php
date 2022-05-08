<?php
/**
 * @author
 * @license MIT
 */
namespace fate\model\validators;

/**
 * 多个值是否完全相等
 *
 * class XxxModel extends Model {
 *      rules() {
 *          return [
 *              [
 *                  'rule' => 'fate\model\validators\EqualValidator',
 *                  'attributes' => ['password', 'confirming'],
 *                  'messages' => ['password error']
 *              ]
 *          ];
 *      }
 * }
 *
 */
class EqualValidator extends \fate\model\Validator {

    /**
     * {@inheritdoc}
     */
    public function validate($attributeName, $attributeValue) {
        $hasError = false;
        $validatingAttributes = $this->attributes;
        $firstValue = $attributeValue;
        $info = $this->getMessage($attributeName);

        $this->skip = true;

        for($i=1; $i<count($validatingAttributes); $i++) {
            if($firstValue !== $this->model->attributes[ $validatingAttributes[$i] ]) {
                $hasError = true;
                break;
            }
        }

        if($hasError) {
            return '' === $info ? implode(', ', $this->attributes) . ' are not equal' : $info;
        }

        return '';
    }

}
