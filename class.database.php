<?php

class Database {

    private $oPDO;

    public function __construct($dsn, $username = false, $passwd = false) {
        if ($username !== false && $passwd !== false) {
            $this->oPDO = new PDO($dsn, $username, $passwd);
        } else if ($username !== false) {
            $this->oPDO = new PDO($dsn, $username, "");
        } else {
            $this->oPDO = new PDO($dsn, "", "");
        }
    }

    public static function NOW($date = null) {
        $date = $date == null ? time() : $date;
        return date('Y-m-d H:i:s', $date);
    }

    public function Insert($table, $input) {
        $table = explode(', ', $table);
        $table = implode('`,`', $table);
        $namex = "";
        $valuex = "";
        for ($i = 0; $i < count($input); $i++) {
            $inps = array_keys($input);
            if ($i === 0) {
                $namex = "`" . $inps[$i] . "`";
                $valuex = ":value" . $i;
            } else {
                $namex .= ", `" . $inps[$i] . "`";
                $valuex .= ", :value" . $i;
            }
        }

        $query = "INSERT INTO `" . $table . "` (" . $namex . ") VALUES ( " . $valuex . ")";

        $Statement = $this->oPDO->prepare($query);

        for ($i = 0; $i < count($input); $i++) {
            $vzxx = array_values($input);
            $vzxx = $vzxx[$i];
            $xco = ":value" . $i;
            if ($vzxx === null) {
                $Statement->bindValue($xco, NULL, PDO::PARAM_NULL);
            } else {
                $Statement->bindValue($xco, $vzxx);
            }
        }

        $Statement->execute();
        return $this->oPDO->lastInsertId();
    }

    public function QueryOutputFetch($query, $redefname, $limit = false) {
        $Statement = $this->oPDO->prepare($query);
        for ($i = 0; $i < count($redefname); $i++) {
            $vzxx = array_values($redefname);
            $vzxs = array_keys($redefname);
            $vzxx = $vzxx[$i];
            $vzxs = $vzxs[$i];
            $xco = $vzxs;
            $Statement->bindValue($xco, $vzxx);
        }
        $Statement->execute();
        if ($limit === true || $limit === 1) {
            return $Statement->fetch();
        } else {
            $output = array();
            while ($row = $Statement->fetch()) {
                array_push($output, $row);
            }
            return $output;
        }
    }

    public function QueryOutputId($query, $redefname) {
        $Statement = $this->oPDO->prepare($query);
        for ($i = 0; $i < count($redefname); $i++) {
            $vzxx = array_values($redefname);
            $vzxs = array_keys($redefname);
            $vzxx = $vzxx[$i];
            $vzxs = $vzxs[$i];
            $xco = $vzxs;
            $Statement->bindValue($xco, $vzxx);
        }
        $Statement->execute();
        return $this->oPDO->lastInsertId();
    }

    public function Select($table, $name, $where = false, $limit = false, $orderby = false, $asc = true) {
        $table = explode(', ', $table);
        $table = implode('`,`', $table);
        if ($where !== false) {
            $wherex = "";
            $q = array();
            $wherex .= $this->QueryRecursive($q, $where);
            
            $query = "SELECT " . ($name[0] != "*" ? '`' . implode('`,`', $name) . "`" : $name[0]) . " FROM `" . $table . "` WHERE " . $wherex . (($orderby !== false) ? " ORDER by " . $orderby . (($asc) ? " ASC " : " DESC ") : "") . (($limit !== false) ? " LIMIT " . $limit : "");
        } else {
            $query = "SELECT " . ($name[0] != "*" ? '`' . implode('`,`', $name) . "`" : $name[0]) . "  FROM `" . $table . "`" . (($orderby !== false) ? " ORDER by " . $orderby . (($asc) ? " ASC " : " DESC ") : "") . (($limit !== false) ? " LIMIT " . $limit : "");
        }
        $Statement = $this->oPDO->prepare($query);
        if ($where !== false) {
            foreach ($q as $key => $where) {
                $vzxx = $where;
                $xco = $key;
                $Statement->bindValue($xco, $vzxx);
            }
        }
        $Statement->execute();
        if ($limit === true || $limit === 1) {
            return $Statement->fetch();
        } else {
            $output = array();
            while ($row = $Statement->fetch()) {
                array_push($output, $row);
            }
            return $output;
        }
    }

    public function SelectCount($table, $where = false) {
        if ($where !== false) {
            $wherex = "";
            $q = array();
            $wherex .= $this->QueryRecursive($q, $where);
            $query = "SELECT COUNT(*) FROM `" . $table . "` WHERE " . $wherex;
        } else {
            $query = "SELECT COUNT(*)  FROM `" . $table . "`";
        }

        $Statement = $this->oPDO->prepare($query);

        if ($where !== false) {
            foreach ($q as $key => $where) {
                $vzxx = $where;
                $xco = $key;
                $Statement->bindValue($xco, $vzxx);
            }
        }

        $Statement->execute();
        return $Statement->fetchColumn();
    }

    public function Delete($table, $where) {
        $table = explode(', ', $table);
        $table = implode('`,`', $table);
        $wherex = "";
        $q = array();
        $wherex .= $this->QueryRecursive($q, $where);
        $query = "DELETE FROM `" . $table . "` WHERE " . $wherex;

        $Statement = $this->oPDO->prepare($query);
        foreach ($q as $key => $where) {
            $vzxx = $where;
            $xco = $key;
            $Statement->bindValue($xco, $vzxx);
        }
        $Statement->execute();
    }

    private $selecttypes = array("!", ">", "<", "~", "^", "%");

    private function QueryRecursive(&$statement, $input, $type = false, $layer = 0) {
        $returnstring = "";
        if ($layer > 0) {
            $returnstring .= "(";
        }
        $i = $layer * 1000;
        foreach ($input as $new => $val) {
            $i = $i + 1;
            if ($returnstring != "(" && $returnstring != "") {
                if ($type != false) {
                    $returnstring .= " || ";
                } else {
                    $returnstring .= " && ";
                }
            }
            if (is_array($val)) {
    			if ($val === array_values($val)) {
					$returnstring .= "`" . $new . "` IN('".implode("','",  $val). "') ";
				}
				else {
					$returnstring .= $this->QueryRecursive($statement, $val, !$type, $layer + 1);
				}
			} else {
                $value = $new;
                $value = str_replace('.', '', $value);
                if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                    $operator = substr($value, 0, 1);
                    $remainings = substr($value, 1);
                    if ($operator == "!") {
                        $returnstring .= "`" . $remainings . "` != :where" . $i;
                    } else if ($operator == ">") {
                        $returnstring .= "`" . $remainings . "` > :where" . $i;
                    } else if ($operator == "<") {
                        $returnstring .= "`" . $remainings . "` < :where" . $i;
                    } else if ($operator == "~") {
                        $returnstring .= "`" . $remainings . "` LIKE :where" . $i;
                    } else if ($operator == "^") {
                        $returnstring .= "`" . $remainings . "` >= :where" . $i;
                    } else {
                        $returnstring .= "`" . $remainings . "` <= :where" . $i;
                    }
                } else {
                    $returnstring .= "`" . $value . "` = :where" . $i;
                }
                $statement[":where" . $i] = $val;
            }
        }
        if ($layer > 0) {
            $returnstring .= ")";
        }
        return $returnstring;
    }

    public function Update($table, $where, $input) {
        $valuex = "";
        $table = explode(', ', $table);
        $table = implode('`,`', $table);
        for ($i = 0; $i < count($input); $i++) {
            $inps = array_keys($input);
            if ($i === 0) {
                $valuex .= "`" . $inps[$i] . "` = :value" . $i;
            } else {
                $valuex .= ", `" . $inps[$i] . "` = :value" . $i;
            }
        }
        $wherex = "";
        $q = array();
        $wherex .= $this->QueryRecursive($q, $where);
        $query = "UPDATE `" . $table . "` SET " . $valuex . " WHERE " . $wherex;


        $Statement = $this->oPDO->prepare($query);

        for ($i = 0; $i < count($input); $i++) {
            $vzxx = array_values($input);
            $vzxx = $vzxx[$i];
            $xco = ":value" . $i;
            $Statement->bindValue($xco, $vzxx);
        }

        foreach ($q as $key => $where) {
            $vzxx = $where;
            $xco = $key;
            $Statement->bindValue($xco, $vzxx);
        }
        $Statement->execute();
    }

}

?>
