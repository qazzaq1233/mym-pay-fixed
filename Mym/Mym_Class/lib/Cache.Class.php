<?php
//namespace lib;

class CACHE {
	public function save($value) {
		if (is_array($value)) $value = serialize($value);
		global $DB;
		$value = addslashes($value);
		return $DB->query("REPLACE INTO `pay_config` SET `v`='$value',`k`='cache'");
	}
	public function pre_fetch(){
		global $_CACHE, $DB;
		$_CACHE=array();
		$row = $DB->query("SELECT `v` FROM `pay_config` WHERE `k`='cache' limit 1")->fetch();
		if($row && !empty($row['v'])){
			$cache = @unserialize($row['v']);
			if(is_array($cache)){
				$_CACHE = $cache;
				return $_CACHE;
			}
		}
		$_CACHE = $this->update();
		return $_CACHE;
	}
	public function update() {
		global $DB;
		$cache = array();
		$query = $DB->query("SELECT * FROM `pay_config` WHERE `k`<>'cache'");
		while($result = $query->fetch()){
			$cache[ $result['k'] ] = $result['v'];
		}
		$this->save($cache);
		return $cache;
	}
	public function clear() {
		global $DB;
		return $DB->query("update `pay_config` set `v`='' where `k`='cache'");
	}
}

function saveSetting($k, $v){
	global $DB;
	if(is_array($v)){
	    $v = json_encode($v);
	}else{
	    $v = htmlspecialchars(daddslashes($v));
	}

	return $DB->query("REPLACE INTO `pay_config` SET `v`='$v',`k`='$k'");
}
