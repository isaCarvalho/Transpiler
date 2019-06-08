<?php

class Query
{
	private static $conn;

	public static function conn()
	{
		try
		{
			self::conn = new PDO('mysql:host=localhost;dbname=transpiler', 'root', '');
			self::conn->setAtribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return self::conn;
		}
		catch(\PDOException $err)
		{
			die('Erro: '.$err->getMessage());
		}
	}

	public static function select($table, $values, $condition, $array = [])
	{
		$quey = "SELECT $values FROM $table WHERE $condition";

		$stmt = self::conn->prepare($query);
		$stmt->execute($array);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}