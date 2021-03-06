<?php
namespace repository;
use entity\Player;
use repository\DataSource;


class PlayerDao extends DataSource {

	function insert(Player $player){
	     $sql = "insert into player values(uuid(),'".$player->getLogin()."','".$player->getPassword()."','".$player->getEmail()."')";
	     parent::insertEntity($sql);
	 }

	 function update(Player $player){
		$sql = "update player set login ='".$player->getLogin()."', password ='".$player->getPassword()."', email = '".$player->getEmail()."' WHERE uuid ='".$player->getUuid()."'";
		parent::updateEntity($sql);
	}

	function findAll(){
	     $sql = "select * from player";
	     return parent::findAllEntity($sql);
	}

	function findOne($uid){
	     $sql = "select * from player where uuid ='".$uid."'";
	     return parent::findOneEntity($sql);
	}

	function delete($uid){
		 $sql = "delete from player where uuid ='$uid'";
		 parent::deleteEntity($sql);
	}

	function signIn($login,$password){
		 $sql = "select login, email  from player where login ='$login' and password ='$password'";
		 return parent::findOneEntity($sql);
	}
}