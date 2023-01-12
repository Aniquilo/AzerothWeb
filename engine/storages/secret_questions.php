<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class SecretQuestionData
{
	public $data = array(
		'1' => '¿En qué ciudad naciste?',
		'2' => '¿En qué ciudad nació tu madre? ',
		'3' => '¿En qué ciudad nació tu padre?',
		'4' => '¿Cuál fue la marca de tu primer coche?',
		'5' => '¿Mejor amig@ en la escuela?',
		'6' => '¿Cuál es el nombre de tu primera escuela?',
		'7' => '¿Cuál fue el primer nombre de tu personaje en el WoW?',
		'8' => '¿Cuál es tu color favorito?',
		'9' => '¿Nombre de tu primera mascota?',
	);

	public function __construct()
	{
		return true;
	}
	
	public function get($key)
	{
		if (!isset($this->data[$key]))
		{
			return false;
		}
		
		return $this->data[$key];
	}
	
	public function __destruct()
	{
		unset($this->data);
		return true;
	}
}
