<?php
namespace y\web;

/**
 * 前端请求
 */
class Request {
	
	/**
     * @var Object $instance 实例
     */
	private static $instance = null;
	
	/**
     * 私有构造方法
     */
	private function __construct() {}
	
	/**
     * 获得单例对象
     */
	public function getInstance() {
		if(null === self::$instance) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * 解析路由
	 */
	public function parseUrl($routeParam) {
		if( isset($_GET[$routeParam]) ) {
			return $_GET[$routeParam];
		}
		
		return '';
	}
}

