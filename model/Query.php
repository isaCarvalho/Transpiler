<?php

// Classe de conexão do PHP com o PostgreSQL;
class Query
{
	private static $conn;

	public static function conn()
	{
		try
		{
			self::$conn = new PDO("pgsql:host=localhost;dbname=transpiler;user=postgres;password=123456");	
			
			self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return self::$conn;
		}
		catch (\PDOException $err)
		{
			die('Erro: '.$err->getMessage());
		}
	}

	public static function select($values, $table, $condition, $array = [])
	{
		$query = "SELECT $values FROM $table WHERE $condition";

		$stmt = self::conn()->prepare($query);
		$stmt->execute($array);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}