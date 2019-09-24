<?php
/**
 * @author
 * @license MIT
 */
namespace fate\core;

use Fate;

/**
 * 组件类是实现 属性 (property) 行为 (behavior) 事件 (event) 的基类
 */
class Component extends Event {

    /**
     * @property array the attached behaviors
     *
     * [
     *     'behaviorName' => BehaviorInstance
     *     ...
     * ]
     *
     */
    public $behaviorsMap = [];

    /**
     * construct
     */
    public function __construct() {
        $this->ensureDeclaredBehaviorsAttached();
    }

    /**
     * 读取不存在或私有的属性
     *
     * @param string $name 属性名
     */
    public function __get($name) {
        foreach($this->behaviorsMap as $instance) {
            if($instance->hasProperty($name)) {
                return $instance->$name;
            }
        }

        throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
    }

    /**
     * 调用不存在或私有的方法
     *
     * @param string $name 方法名字
     * @param array $params 方法参数
     */
    public function __call($name, $params) {
        foreach($this->behaviorsMap as $instance) {
            if($instance->hasMethod($name)) {
                return call_user_func_array([$instance, $name], $params);
            }
        }

        throw new InvalidCallException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * 声明该组件的行为列表
     *
     * 子类组件可以重写该方法去指定要附加的行为类
     *
     * @return array
     *
     * [
     *     'behaviorName' => [
     *         'classPath' => 'BehaviorClassName',
     *         'property1' => 'value1',
     *         'property2' => 'value2'
     *     ],
     *     'behaviorName2' => 'BehaviorClassName2'
     *     'behaviorName3' => BehaviorClassInstance
     * ]
     *
     */
    public function behaviors() {
        return null;
    }

    /**
     * 确保 behaviors() 声明的行为已保存到组件
     */
    public function ensureDeclaredBehaviorsAttached() {
        $behaviors = $this->behaviors();

        if(null === $behaviors) {
            return;
        }

        foreach($behaviors as $name => $val) {
            $this->attachBehaviorInternal($name, $val);
        }
    }

    /**
     * 保存行为类到组件
     *
     * @param string name 行为的名称
     * @param string | object | array behavior
     */
    public function attachBehaviorInternal($name, $behavior) {
        if(!($behavior instanceof Behavior)) {
            $behavior = Fate::createObject(behavior);
        }

        if(isset($this->behaviorsMap[$name])) {
            $this->behaviorsMap[$name]->unListen();
        }

        // 行为类可以监听组件的事件并处理
        $behavior->listen($this);
        $this->behaviorsMap[$name] = $behavior;
    }

}
