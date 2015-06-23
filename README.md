# PHP Database Documentation
  
## Construct DB Class
```php  
include_once('class.database.php'); //The location of the database class  
$dbhost = "localhost"; //Hostname  
$dbname = "myfirstdatabase"; //Database name  
$dbuser = "root"; //Database user  
$dbpass = ""; //Database user password  
$db = new Database("mysql:host=".$dbhost.";dbname=".$dbname. ";", $dbuser, $dbpass);
```

## Insert Method #1
### Code Pattern
```php  
$db->Insert(string TableName, array Input);
```
### Example
```php  
$db->Insert('tablename', array('email' => "example@email.com"));
```


## Insert Method #2
### Code Pattern
```php  
$db->Insert(string TableName, array InputNames, array InputArray);
```
### Example
```php  
$db->Insert('tablename', array('email'), array("example@email.com"));
```


## Insert Method #3
### Code Pattern
```php  
$db->Insert(string TableName, array InputNames, array InputArray);
```
### Example
```php  
$db->Insert('tablename', array('email'), array(array("example@email.com"),array("example2@email.com")));
```


## Update
### Code Pattern
```php  
$db->Update(string TableName, array Where, array Input);
```
### Example
```php  
$db->Update('tablename', array('email' => "example@email.com"), array('email' => ""));
```

## Delete
### Code Pattern
```php  
$db->Delete(string TableName, array Where);
```
### Example
```php  
$db->Delete('tablename', array('email' => "example@email.com"));
```

## Select
### Code Pattern
```php  
$db->Select(string TableName, array What, optional array Where, optional (default: 0) int/bool limit , optional (default: empty) string OrderBy, optional (default: true) bool Ascending);
```
### Example
```php  
$db->Select('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1")));
```
## Select One
### Code Pattern
```php  
$db->SelectOne(string TableName, array What, optional array Where, optional (default: empty) string OrderBy, optional (default: true) bool Ascending);
```
### Example
```php  
$db->SelectOne('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1")));
```
## Select Distinct
### Code Pattern
```php  
$db->SelectDistinct(string TableName, array What, optional array Where, optional (default: 0) int/bool limit , optional (default: empty) string OrderBy, optional (default: true) bool Ascending);
```
### Example
```php  
$db->SelectDistinct('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1")));
```
## Select Distinct One
### Code Pattern
```php  
$db->SelectDistinctOne(string TableName, array What, optional array Where, optional (default: empty) string OrderBy, optional (default: true) bool Ascending);
```
### Example
```php  
$db->SelectDistinctOne('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1")));
```
## SelectCount
### Code Pattern
```php  
$db->SelectCount(string TableName, array Where);
```
### Example
```php  
$db->SelectCount('tablename', array('email' => "example@email.com"));
```

## NOW
### Default
```php  
Database::NOW();
```

## Where Statement
### Default
```php  
$where = array();
```
### Equals
```php  
$where = array("id" => "1"); // id = '1'
```
### Not Equals
```php  
$where = array("!id" => "1"); // id != '1'
```
### Not Equals
```php  
$where = array("<>id" => "1"); // id != '1'
```
### Above
```php  
$where = array(">id" => "1"); // id > '1'
```
### Below
```php  
$where = array("<id" => "1"); // id < '1'
```
### Above or Equals
```php  
$where = array("^id" => "1"); // id >= '1'
```
### Above or Equals
```php  
$where = array(">=id" => "1"); // id >= '1'
```
### Below or Equals
```php  
$where = array("%id" => "1"); // id <= '1'
```
### Below or Equals
```php  
$where = array("<=id" => "1"); // id <= '1'
```
### Like
```php  
$where = array("~id" => "1"); // id LIKE '1'
```
### Like
```php  
$where = array("%=id" => "1"); // id LIKE '1'
```
### Not Like
```php  
$where = array("?id" => "1"); // id NOT LIKE '1'
```
### Not Like
```php  
$where = array("!~id" => "1"); // id NOT LIKE '1'
```

### IN
```php
$where = array('id' => array(1,2)); // id IN ('1', '2')
```
### NOT IN
```php
$where = array('!id' => array(1,2)); // id NOT IN ('1', '2')
```
### BETWEEN
```php
$where = array('~id' => array(1,2)); // id BETWEEN ('1', '2')
```
### NOT BETWEEN
```php
$where = array('?id' => array(1,2)); // id BETWEEN ('1', '2')
```
### NOT BETWEEN
```php
$where = array('!~id' => array(1,2)); // id BETWEEN ('1', '2')
```
### AND
```php  
$where = array("id" => "1", "name" => "Dominic"); // id = '1' AND name = 'Dominic'
```
### OR
```php  
$where = array("name" => "Dominic", array("id" => "1", "id" => "2")); // name = 'Dominic' AND (id = '1' OR id = '2')
```
### OR AND
```php  
$where = array("name" => "Dominic", array("id" => "1", array("id" => "2", "key" => 1))); // name = 'Dominic' AND (id = '1' OR (id = '2' AND key = '1'))
```
