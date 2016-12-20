<?php

namespace App;

class User
{
	
	private $dbh;
	
	protected $data = [];
	
	public function __construct($dbh, $userId = null)
	{
		$this->dbh = $dbh;
		
		if ($userId !== null)
			$this->data = $this->loadUser($userId);
	}
	
	/**
	 * Yay for magic function
	 */
	public function __get($key)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}
	
	public function loadUser($userId)
	{
		$getUser = $this->dbh->prepare("
			SELECT *
			FROM users
			WHERE user_id = :userId
		");
		$getUser->execute([
			":userId" => $userId,
		]);
		
		return $getUser->fetch(\PDO::FETCH_ASSOC);
	}
	
}
