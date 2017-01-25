<?php

namespace App;

class User
{
	
	private $dbh;
	
	public $activeTicket = null;
	
	protected $data = [];
	
	public function __construct($dbh, $userId = null)
	{
		$this->dbh = $dbh;
		
		if ($userId !== null)
			$this->data = $this->loadUser($userId);
	}
	
	public function __destruct()
	{
		$this->update();
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
	
	public function update()
	{
		if (empty($this->data))
			return;
		
		$updateUser = $this->dbh->prepare("
			UPDATE users
			SET date_seen = UNIX_TIMESTAMP(NOW()), active_ticket = :ticket
			WHERE user_id = :userId
		");
		$updateUser->execute([
			":ticket" => $this->activeTicket,
			":userId" => $this->user_id,
		]);
	}
	
}
