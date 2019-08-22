<?php

// Classe de conexão do PHP com o PostgreSQL;
class Query
{
	private static $conn;

	public static function conn()
	{
		try
		{
			self::$conn = new PDO("pgsql:host=ec2-23-23-228-132.compute-1.amazonaws.com;dbname=dc386mrb3o0ucn;user=moovthhsothwbg;password=39efea7b8b62cd7ecfd980e462aaf8c6a4731ed96f9dcfabfc0ed09f8c7b4dd1");
			
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