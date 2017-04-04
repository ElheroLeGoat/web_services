<?php
require("config.php");

/**
* Gets specified data in a table and returns them
*
* @param
*        .string - $table
*        .array  - $table_cols
*        .string - $clause
*
* @return Array on complete | false on failure
*/
function get_table($table, array $table_cols, $clause = "WHERE 1") {
	global $mysqli;
	if (is_array($table_cols)) {
		$colstring = implode(", ", $table_cols);
	}
	$sql = "SELECT $colstring FROM $table $clause";
			if ($rs = $mysqli->query($sql)) {
				$data = array();
				while ($row = $rs->fetch_assoc()) {
					$data[] = $row;
				}
				$rs->free();
			}
			$data = array_filter($data);
			if (empty($data)) {
				return false;
			}
			else {
				return $data;
			}
}

/**
* Inserts an row into an database,
*
* @param string - $sql
*
* @return int (row id) on complete | false on failure
*/
function create_row($sql) {
	$e = true;
	global $mysqli;

	if ($stmt = $mysqli->prepare($sql)) {
		if (!$stmt->execute()) {
			$e = false;
		}
		else {
			$e = $mysqli->insert_id;
		}
		$stmt->close();
	}
	else {
		$e = false;
	}
	return $e;
}
/**
* Validates a string and makes sure it is prepared for the database
*
*
* @param
* 		.string - $string
*			.string - $name
*			.string - $type (optional)
*			.int	- $maxlen (optional)
*			.int	- $minlen (optional)
*
* @return  string - $string on complete | false on failure
*/
function validateString($string, $name, $type = "string", $maxLen = 45, $minLen = 3) {
	$errors = array();
	$strLen = strlen($string);
	$val 	= true;
	if ($type == "int" && is_numeric( $string ) == false ) {
		$errors[] = "FEJL! $name må kun indholde tal";
		$val			= false;
	}

	if ($strLen > $maxLen && $maxLen !== 0) {
		$val = false;
		$errors[] = "FEJL! $name må maks være $maxLen karaktere langt";
	}
	if ($strLen < $minLen) {
		$val = false;
		$errors[] = "FEJL! $name skal minimum være $minLen karaktere langt";
	}
	$sca = htmlspecialchars($string);
	$sca = addslashes($sca);
	if (!$val == true) {
		return array("success" => 0, "errors" => $errors, "string" => $string);
	}
	else if ($val == true) {
		return array("success" => true, "modified" => $sca, "string" => $string);
	}
}
?>