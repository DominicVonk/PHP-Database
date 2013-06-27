<?php

/*
 *      #     #                             ######  ######  
 *      ##   ##  ####  #    #  ####   ####  #     # #     # 
 *      # # # # #    # ##   # #    # #    # #     # #     # 
 *      #  #  # #    # # #  # #      #    # #     # ######  
 *      #     # #    # #  # # #  ### #    # #     # #     # 
 *      #     # #    # #   ## #    # #    # #     # #     # 
 *      #     #  ####  #    #  ####   ####  ######  ######   
 *
 * 
 *      Developed by Dominic Vonk
 *      Date: 18-6-2013
 *      Hypertext PreProcessor Mongo Database
 *      Version 0.0.1 BETA
 *      Readme:  https://github.com/Lacosta/PHP-Database/class.mongoDB.php
 */

class PHPMongoDB {

    private $db;

    public function __construct($dbname) {
        $mongo = new MongoClient();
        $this->db = $mongo->selectDB($dbname);
    }

    public static function NOW($date = null) {
        $date = $date == null ? time() : $date;
        return date('Y-m-d H:i:s', $date);
    }

    public function Insert($table, $insertKeys, $insertValues = null) {
        $variables = array();
        $table = $this->db->selectCollection($table);
        $output = null;
        if (is_array($insertValues) && $insertValues !== null) {
            if (is_array($insertValues[0])) {
                $output = array();
                foreach ($insertValues as $iv) {
                    $variables = array_combine($insertKeys, $iv);

                    $table->insert($variables);
                    $output[] = $variables["_id"]."";
                }
            } else {
                $variables = array_combine($insertKeys, $insertValues);

                $table->insert($variables);
                $output = $variables["_id"]."";
            }
        } else {
            $variables = $insertKeys;

            $table->insert($variables);
            $output = $variables["_id"]."";
        }



        return $output;
        // return $this->oPDO->lastInsertId();
    }

    private $selecttypes = array("!", ">", "<", "~", "^", "%", ">=", "<=", "%=");

    public function Select($table, $name, $where = false, $limit = false, $orderby = false, $asc = true) {
        if ($where !== false) {
            $q = array();
            $wherex = $this->QueryRecursive($q, $where);
        }
        if ($where === false) {
            $output = $this->db->selectCollection($table)->find(array(), $name);
        } else {
            if ($name !== array("*")) {
                $output = $this->db->selectCollection($table)->find($wherex['$and'], $name);
            } else {
                $output = $this->db->selectCollection($table)->find($wherex['$and']);
            }
        }
        if ($limit === false) {
            return iterator_to_array($output);
        } else {
            if ($limit === true || $limit === 1) {
                if ($orderby !== false) {
                    return $output->sort(array($orderby => ($asc) ? 1 : -1))->limit($limit)->getNext();
                } else {
                    return $output->limit($limit)->getNext();
                }
            } else {
                if ($orderby !== false) {
                    return iterator_to_array($output->sort(array($orderby => ($asc) ? 1 : -1))->limit($limit));
                } else {
                    return iterator_to_array($output->limit($limit));
                }
            }
        }
    }

    public function SelectDistinct($table, $name, $where = false, $limit = false, $orderby = false, $asc = true) {
        if ($where !== false) {
            $q = array();
            $wherex = $this->QueryRecursive($q, $where);
        }
        if ($where === false) {
            $output = $this->db->selectCollection($table)->distinct($name);
        } else {

            $output = $this->db->selectCollection($table)->distinct($name, $wherex['$and']);
        }
        if ($limit === false) {
            return iterator_to_array($output);
        } else {
            if ($limit === true || $limit === 1) {
                if ($orderby !== false) {
                    return $output->sort(array($orderby => ($asc) ? 1 : -1))->limit($limit)->getNext();
                } else {
                    return $output->limit($limit)->getNext();
                }
            } else {
                if ($orderby !== false) {
                    return iterator_to_array($output->sort(array($orderby => ($asc) ? 1 : -1))->limit($limit));
                } else {
                    return iterator_to_array($output->limit($limit));
                }
            }
        }
    }

    public function SelectCount($table, $where = false) {
        if ($where !== false) {
            $q = array();
            $wherex = $this->QueryRecursive($q, $where);
        }

        $output = $this->db->selectCollection($table)->find($wherex['$and'])->count();
        return $output;
    }

    public function Delete($table, $where) {
        if ($where !== false) {
            $q = array();
            $wherex = $this->QueryRecursive($q, $where);
        }

        $this->db->selectCollection($table)->remove($wherex['$and']);
    }

    public function QueryRecursive(&$statement, $input, $type = false, $layer = 0) {

        $returnstring = array();

        $i = $layer * 1000;
        $i = $i + 1;
        $jajadeze = "";

        if ($type != false) {
            $returnstring['$or'] = array();
            $jajadeze = '$or';
        } else {
            $returnstring['$and'] = array();
            $jajadeze = '$and';
        }
        foreach ($input as $new => $val) {


            if (is_array($val)) {
                if ($jajadeze !== "") {
                    $returnstring[$jajadeze] += $this->QueryRecursive($statement, $val, !$type, $layer + 1);
                }
            } else {
                $value = $new;
                $value = str_replace('.', '', $value);
                if (in_array(substr($value, 0, 1), $this->selecttypes) || in_array(substr($value, 0, 2), $this->selecttypes)) {
                    $operator = substr($value, 0, 1);
                    $remainings = substr($value, 1);
                    if ($operator == "!") {
                        $returnstring[$jajadeze][$remainings] = array('$ne' => $val);
                        ;
                    } else if ($operator == ">") {
                        $returnstring[$jajadeze][$remainings] = array('$gt' => $val);
                    } else if ($operator == "<") {
                        $returnstring[$jajadeze][$remainings] = array('$lt' => $val);
                    } else if ($operator == "~") {

                        $returnstring[$jajadeze][$remainings] = array('$regex' => str_replace('%', '.*', $val));
                    } else if ($operator == "^") {
                        $returnstring[$jajadeze][$remainings] = array('$gte' => $val);
                    } else {
                        if ($operator == ">=") {
                            $returnstring[$jajadeze][$remainings] = array('$gte' => $val);
                        } else if ($operator == "%=") {
                            $returnstring[$jajadeze][$remainings] = array('$regex' => str_replace('%', '.*', $val));
                        } else {
                            $returnstring[$jajadeze][$remainings] = array('$lte' => $val);
                        }
                    }
                } else {
                    $returnstring[$jajadeze][$value] = $val;
                }
            }
        }
        return $returnstring;
    }

    public function Update($table, $where, $input) {
        if ($where !== false) {
            $q = array();
            $wherex = $this->QueryRecursive($q, $where);
        }

        $this->db->selectCollection($table)->update($wherex['$and'], array('$set' => $input));
    }

}
