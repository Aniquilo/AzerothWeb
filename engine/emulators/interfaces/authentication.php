<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

interface emulator_Authentication
{
    public function __construct();
    public function makeHash($identity, $password);
    public function getAccountById($id);
    public function getAccountByIdentity($identity);
    public function getAccountByEmail($email);
    public function getAccountByUsername($Username);
    public function changePassword($id, $identity, $password);
    public function changeEmail($id, $email);
    public function register($identity, $password, $email, $recruiter);
    public function getACPUserDetails($id);
    public function getTable($name);
    public function getColumn($table, $name);
    public function getAllColumns($table);
}