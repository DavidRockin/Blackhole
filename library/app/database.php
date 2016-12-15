<?php

namespace App;

class Database extends \PDO
{

    public function __construct($config)
    {
        parent::__construct($config['dsn'], $config['user'], $config['pass']);
    }

}
