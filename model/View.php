<?php

/**
 * Class View
 * 
 * View class.
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class View
{
	private $id;			// Int
	private $pid;			// INT
	private $lid;			// INT
	private $view_str;		// String
	private $right_level;	// INT
	
	function __construct()
	{
		$this->id = 0;
		$this->view_str = "";
		$this->pid = 0;
		$this->lid = 0;
	}
	
	function getId() {
		return $this->id;
	}
	function setId($id) {
		$this->id = $id;
	}
	
	function getPid() {
		return $this->pid;
	}
	function setPid($pid) {
		$this->pid = $pid;
	}
	
	function getLid() {
		return $this->lid;
	}
	function setLid($lid) {
		$this->lid = $lid;
	}
	
	function getViewStr() {
		return $this->view_str;
	}
	function setViewStr($view_str) {
		$this->view_str = $view_str;
	}
	
	function getRightLevel() {
		return $this->right_level;
	}
	function setRightLevel($right_level) {
		$this->right_level = $right_level;
	}
}
?>
