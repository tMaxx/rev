<?php ///r3vCMS /sys/DB/.php
namespace r3v\DB;

class Error extends \Error {
	protected $inst = NULL;

	public function __construct($msg = NULL, $inst = NULL) {
		if (is_object($inst))
			$this->inst = $inst;
		parent::__construct($msg);
	}

	public function getExtMessage() {
		$r = parent::getMessage();
		$s = Base::getErrors();
		if (!$s && isset($this->stmt))
			$s = $this->getStmtErrors();
		if ($s)
			$r .= ', details: "' . $s . '"';
		return $r;
	}
}