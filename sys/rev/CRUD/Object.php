<?php ///rev engine \rev\CRUD\Object
namespace rev\CRUD;
use rev\Error;

/**
 * CRUD for single object
 * Manages rev\Form instance, handles field hooks
 */
class Object {
	protected $def;

	protected $values = [];
	protected $id = null;
	protected $dbo = null;
	protected $form = null;

	function __construct($def) {
		unset($def['fields']['id']);
		$this->def = $def;
		unset($this->def['fields']['submit']);

		$this->dbo = new \rev\DB\Table($def['table']);

		$this->form = new \rev\Form($def);
		if ($this->form->submitted) {
			$ff = $this->form->fields;
			foreach ($this->values as $k => $_)
				$this->values[$k] = $ff[$k]->raw_value;
		}
	}

	/**
	 * Launch hooks by field name
	 * @param $name field's property name
	 */
	protected function _processHooks($name) {
		foreach ($this->def['fields'] as $k => $v)
			if (!empty($v[$name])) {
				$cmd = explode(' ', $v[$name], 2);

				if ($cmd[0] == 'set') {
					if (strtolower($cmd[1]) == 'now')
						$this->values[$k] = NOW;
				} elseif ($cmd[0] == 'call') {
					call_user_func($cmd[1], $this->values[$k]);
				}
			}
	}

	public function load($id) {
		$this->values = $this->dbo->select()->where(['id' => $id])->row();
		$this->dbo->clear();

		if ($this->values) {
			unset($this->values['id']);
			$this->id = $id;
		}
		return !empty($this->values);
	}

	public function save() {
		$this->_processHooks('pre_save');
		if ($this->id)
			$this->dbo->where(['id' => $this->id])->update($this->values)->exec();
		else
			$this->id = $this->dbo->insert($this->values)->iid();
		$this->dbo->clear();
	}

	public function delete() {
		if ($this->id) {
			$this->dbo->delete()->where(['id' => $this->id])->exec();
			$this->dbo->clear();
		}
	}

	public function create() {
		//fill with empty data
		$this->values = array_fill_keys(array_keys($def['fields']), null);

		$this->id = null;
		//TODO: all
		$this->_processHooks('on_create');

		return $this;
	}

	public function render() {
		$this->form->render();
	}

	public function __get($name) {
		if ($name == 'id' || $name == 'values')
			return $this->{$name};
		if ($name == 'submitted')
			return $this->form->submitted;
		if (!isset($this->values[$name]))
			throw new Error("CRUD Obj: no such field name: $name");
		return $this->values[$name];
	}

	public function __set($name, $val) {
		if ($name == 'id')
			$this->id = $val;
		if ($name == 'values' && is_array($val)) {
			foreach ($val as $k => $v)
				if (array_key_exists($k, $this->values))
					$this->values[$k] = $v;
		}
		elseif (!isset($this->values[$name]))
			throw new Error("CRUD Obj: no such field name: $name");
		return ($this->values[$name] = $val);
	}
}
