<?php

/*
 *      ######  #     # ######     ######                                                  
 *      #     # #     # #     #    #     #   ##   #####   ##   #####    ##    ####  ###### 
 *      #     # #     # #     #    #     #  #  #    #    #  #  #    #  #  #  #      #      
 *      ######  ####### ######     #     # #    #   #   #    # #####  #    #  ####  #####  
 *      #       #     # #          #     # ######   #   ###### #    # ######      # #      
 *      #       #     # #          #     # #    #   #   #    # #    # #    # #    # #      
 *      #       #     # #          ######  #    #   #   #    # #####  #    #  ####  ###### 
 *
 * 
 *      Developed by Dominic Vonk
 *      Date: 18-6-2013
 *      Hypertext PreProcessor Database
 *      Version 1.0.1 BETA
 *      Readme:  https://github.com/Lacosta/PHP-Database
 */

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

    public function Insert($table, $insertKeys, $insertValues = null) {
        $variables = array();
        $counts = 0;
        $insertingKeys = $insertValues === null ? array_keys($insertKeys) : $insertKeys;
        $sQuery = 'INSERT INTO ' . $table . ' (`' . implode('`,`', $insertingKeys) . '`) VALUES ';

        if (is_array($insertValues[0]) && $insertValues !== null) {
            foreach ($insertValues as $insertArr) {

                foreach ($insertArr as $ina) {
                    if ($counts % count($insertKeys) == 0) {
                        $sQuery .= '(:value' . $counts . ',';
                    } else if ($counts % count($insertKeys) == count($insertKeys) - 1) {
                        $sQuery .= ':value' . $counts . '),';
                    } else {
                        $sQuery .= ':value' . $counts . ',';
                    }

                    $variables[':value' . $counts] = $ina;
                    $counts++;
                }
            }
            $sQuery = substr($sQuery, 0, strlen($sQuery) - 1);
        } else {
            $insertValues = ($insertValues === null) ? $insertKeys : $insertValues;
            foreach ($insertValues as $ina) {
                if ($counts % count($insertKeys) == 0) {
                    $sQuery .= '(:value' . $counts . ',';
                } else if ($counts % count($insertKeys) == count($insertKeys) - 1) {
                    $sQuery .= ':value' . $counts . ')';
                } else {
                    $sQuery .= ':value' . $counts . ',';
                }

                $variables[':value' . $counts] = $ina;
                $counts++;
            }
        }



        $statement = $this->oPDO->prepare($sQuery);
        $statement->execute($variables);
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

    private $selecttypes = array("!", ">", "<", "~", "^", "%", ">=", "<=", "%=");

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
            $output = array();
            while ($row = $Statement->fetch()) {
                $output = $row;
            }
            return $output;
        } else {
            $output = array();
            $output = $Statement->fetchAll();
            return $output;
        }
    }

    public function SelectDistinct($table, $name, $where = false, $limit = false, $orderby = false, $asc = true) {
        $table = explode(', ', $table);
        $table = implode('`,`', $table);
        if ($where !== false) {
            $wherex = "";
            $q = array();
            $wherex .= $this->QueryRecursive($q, $where);

            $query = "SELECT DISTINCT " . '`' . implode('`,`', $name) . "`" . " FROM `" . $table . "` WHERE " . $wherex . (($orderby !== false) ? " ORDER by " . $orderby . (($asc) ? " ASC " : " DESC ") : "") . (($limit !== false) ? " LIMIT " . $limit : "");
        } else {
            $query = "SELECT DISTINCT " . '`' . implode('`,`', $name) . "`" . "  FROM `" . $table . "`" . (($orderby !== false) ? " ORDER by " . $orderby . (($asc) ? " ASC " : " DESC ") : "") . (($limit !== false) ? " LIMIT " . $limit : "");
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
            $output = array();
            $output = $Statement->fetchAll();
            return $output;
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

    public function QueryRecursive(&$statement, $input, $type = false, $layer = 0) {
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
                $returnstring .= $this->QueryRecursive($statement, $val, !$type, $layer + 1);
            } else {
                $value = $new;
                $value = str_replace('.', '', $value);
                if (in_array(substr($value, 0, 1), $this->selecttypes) || in_array(substr($value, 0, 2), $this->selecttypes)) {
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
                        if ($operator == ">=") {
                            $returnstring .= "`" . $remainings . "` >= :where" . $i;
                        } else if ($operator == "%=") {
                            $returnstring .= "`" . $remainings . "` LIKE :where" . $i;
                        } else {
                            $returnstring .= "`" . $remainings . "` <= :where" . $i;
                        }
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
