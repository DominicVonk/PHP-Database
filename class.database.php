<?php

use PDO;

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

    public static function TimeStamp($date = null) {
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

    public function QueryOutputId($query, $redefname, $limit = false) {
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

            for ($i = 0; $i < count($where); $i++) {
                $inps = array_keys($where);
                if ($i === 0) {
                    $value = $inps[$i];
                    if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                        $operator = substr($value, 0, 1);
                        $remainings = substr($value, 1);
                        if ($operator == "!") {
                            $wherex .= "`" . $remainings . "` != :where" . $i;
                        } else if ($operator == ">") {
                            $wherex .= "`" . $remainings . "` > :where" . $i;
                        } else if ($operator == "<") {
                            $wherex .= "`" . $remainings . "` < :where" . $i;
                        } else if ($operator == "~") {
                            $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                        } else if ($operator == "^") {
                            $wherex .= "`" . $remainings . "` >= :where" . $i;
                        } else {
                            $wherex .= "`" . $remainings . "` <= :where" . $i;
                        }
                    } else {
                        $wherex .= "`" . $value . "` = :where" . $i;
                    }
                } else {
                    $wherex .= " AND ";
                    $value = $inps[$i];
                    if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                        $operator = substr($value, 0, 1);
                        $remainings = substr($value, 1);
                        if ($operator == "!") {
                            $wherex .= "`" . $remainings . "` != :where" . $i;
                        } else if ($operator == ">") {
                            $wherex .= "`" . $remainings . "` > :where" . $i;
                        } else if ($operator == "<") {
                            $wherex .= "`" . $remainings . "` < :where" . $i;
                        } else if ($operator == "~") {
                            $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                        } else if ($operator == "^") {
                            $wherex .= "`" . $remainings . "` >= :where" . $i;
                        } else {
                            $wherex .= "`" . $remainings . "` <= :where" . $i;
                        }
                    } else {
                        $wherex .= "`" . $value . "` = :where" . $i;
                    }
                }
            }
            $query = "SELECT " . ($name[0] != "*" ? '`' . implode('`,`', $name) . "`" : $name[0]) . " FROM `" . $table . "` WHERE " . $wherex . (($orderby !== false) ? " ORDER by " . $orderby . (($asc) ? " ASC " : " DESC ") : "") . (($limit !== false) ? " LIMIT " . $limit : "");
        } else {
            $query = "SELECT " . ($name[0] != "*" ? '`' . implode('`,`', $name) . "`" : $name[0]) . "  FROM `" . $table . "`" . (($orderby !== false) ? " ORDER by " . $orderby . (($asc) ? " ASC " : " DESC ") : "") . (($limit !== false) ? " LIMIT " . $limit : "");
        }
        $Statement = $this->oPDO->prepare($query);
        if ($where !== false) {
            for ($i = 0; $i < count($where); $i++) {
                $vzxx = array_values($where);
                $vzxx = $vzxx[$i];
                $xco = ":where" . $i;
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

            for ($i = 0; $i < count($where); $i++) {
                $inps = array_keys($where);
                if ($i === 0) {
                    $value = $inps[$i];
                    if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                        $operator = substr($value, 0, 1);
                        $remainings = substr($value, 1);
                        if ($operator == "!") {
                            $wherex .= "`" . $remainings . "` != :where" . $i;
                        } else if ($operator == ">") {
                            $wherex .= "`" . $remainings . "` > :where" . $i;
                        } else if ($operator == "<") {
                            $wherex .= "`" . $remainings . "` < :where" . $i;
                        } else if ($operator == "~") {
                            $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                        } else if ($operator == "^") {
                            $wherex .= "`" . $remainings . "` >= :where" . $i;
                        } else {
                            $wherex .= "`" . $remainings . "` <= :where" . $i;
                        }
                    } else {
                        $wherex .= "`" . $value . "` = :where" . $i;
                    }
                } else {
                    $wherex .= " AND ";
                    $value = $inps[$i];
                    if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                        $operator = substr($value, 0, 1);
                        $remainings = substr($value, 1);
                        if ($operator == "!") {
                            $wherex .= "`" . $remainings . "` != :where" . $i;
                        } else if ($operator == ">") {
                            $wherex .= "`" . $remainings . "` > :where" . $i;
                        } else if ($operator == "<") {
                            $wherex .= "`" . $remainings . "` < :where" . $i;
                        } else if ($operator == "~") {
                            $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                        } else if ($operator == "^") {
                            $wherex .= "`" . $remainings . "` >= :where" . $i;
                        } else {
                            $wherex .= "`" . $remainings . "` <= :where" . $i;
                        }
                    } else {
                        $wherex .= "`" . $value . "` = :where" . $i;
                    }
                }
            }
            $query = "SELECT COUNT(*) FROM `" . $table . "` WHERE " . $wherex;
        } else {
            $query = "SELECT COUNT(*)  FROM `" . $table . "`";
        }

        $Statement = $this->oPDO->prepare($query);

        if ($where !== false) {
            for ($i = 0; $i < count($where); $i++) {
                $vzxx = array_values($where);
                $vzxx = $vzxx[$i];
                $xco = ":where" . $i;
                $Statement->bindValue($xco, $vzxx);
            }
        }

        $Statement->execute();
        return $Statement->fetchColumn();
    }

    public function Delete($table, $where) {
        $wherex = "";
        $table = explode(', ', $table);
        $table = implode('`,`', $table);
        for ($i = 0; $i < count($where); $i++) {
            $inps = array_keys($where);
            if ($i === 0) {
                $value = $inps[$i];
                if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                    $operator = substr($value, 0, 1);
                    $remainings = substr($value, 1);
                    if ($operator == "!") {
                        $wherex .= "`" . $remainings . "` != :where" . $i;
                    } else if ($operator == ">") {
                        $wherex .= "`" . $remainings . "` > :where" . $i;
                    } else if ($operator == "<") {
                        $wherex .= "`" . $remainings . "` < :where" . $i;
                    } else if ($operator == "~") {
                        $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                    } else if ($operator == "^") {
                        $wherex .= "`" . $remainings . "` >= :where" . $i;
                    } else {
                        $wherex .= "`" . $remainings . "` <= :where" . $i;
                    }
                } else {
                    $wherex .= "`" . $value . "` = :where" . $i;
                }
            } else {
                $wherex .= " AND ";
                $value = $inps[$i];
                if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                    $operator = substr($value, 0, 1);
                    $remainings = substr($value, 1);
                    if ($operator == "!") {
                        $wherex .= "`" . $remainings . "` != :where" . $i;
                    } else if ($operator == ">") {
                        $wherex .= "`" . $remainings . "` > :where" . $i;
                    } else if ($operator == "<") {
                        $wherex .= "`" . $remainings . "` < :where" . $i;
                    } else if ($operator == "~") {
                        $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                    } else if ($operator == "^") {
                        $wherex .= "`" . $remainings . "` >= :where" . $i;
                    } else {
                        $wherex .= "`" . $remainings . "` <= :where" . $i;
                    }
                } else {
                    $wherex .= "`" . $value . "` = :where" . $i;
                }
            }
        }
        $query = "DELETE FROM `" . $table . "` WHERE " . $wherex;

        $Statement = $this->oPDO->prepare($query);
        for ($i = 0; $i < count($where); $i++) {
            $vzxx = array_values($where);
            $vzxx = $vzxx[$i];
            $xco = ":where" . $i;
            $Statement->bindValue($xco, $vzxx);
        }
        $Statement->execute();
    }

    private $selecttypes = array("!", ">", "<", "~", "^", "%");

    public function Edit($table, $where, $input) {
        $valuex = "";
        $wherex = "";
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
        for ($i = 0; $i < count($where); $i++) {
            $inps = array_keys($where);
            if ($i === 0) {
                $value = $inps[$i];
                if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                    $operator = substr($value, 0, 1);
                    $remainings = substr($value, 1);
                    if ($operator == "!") {
                        $wherex .= "`" . $remainings . "` != :where" . $i;
                    } else if ($operator == ">") {
                        $wherex .= "`" . $remainings . "` > :where" . $i;
                    } else if ($operator == "<") {
                        $wherex .= "`" . $remainings . "` < :where" . $i;
                    } else if ($operator == "~") {
                        $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                    } else if ($operator == "^") {
                        $wherex .= "`" . $remainings . "` >= :where" . $i;
                    } else {
                        $wherex .= "`" . $remainings . "` <= :where" . $i;
                    }
                } else {
                    $wherex .= "`" . $value . "` = :where" . $i;
                }
            } else {
                $wherex .= " AND ";
                $value = $inps[$i];
                if (in_array(substr($value, 0, 1), $this->selecttypes)) {
                    $operator = substr($value, 0, 1);
                    $remainings = substr($value, 1);
                    if ($operator == "!") {
                        $wherex .= "`" . $remainings . "` != :where" . $i;
                    } else if ($operator == ">") {
                        $wherex .= "`" . $remainings . "` > :where" . $i;
                    } else if ($operator == "<") {
                        $wherex .= "`" . $remainings . "` < :where" . $i;
                    } else if ($operator == "~") {
                        $wherex .= "`" . $remainings . "` LIKE :where" . $i;
                    } else if ($operator == "^") {
                        $wherex .= "`" . $remainings . "` >= :where" . $i;
                    } else {
                        $wherex .= "`" . $remainings . "` <= :where" . $i;
                    }
                } else {
                    $wherex .= "`" . $value . "` = :where" . $i;
                }
            }
        }
        $query = "UPDATE `" . $table . "` SET " . $valuex . " WHERE " . $wherex;


        $Statement = $this->oPDO->prepare($query);

        for ($i = 0; $i < count($input); $i++) {
            $vzxx = array_values($input);
            $vzxx = $vzxx[$i];
            $xco = ":value" . $i;
            $Statement->bindValue($xco, $vzxx);
        }

        for ($i = 0; $i < count($where); $i++) {
            $vzxx = array_values($where);
            $vzxx = $vzxx[$i];
            $xco = ":where" . $i;
            $Statement->bindValue($xco, $vzxx);
        }
        $Statement->execute();
    }

}

?>