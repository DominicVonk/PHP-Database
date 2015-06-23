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

## Methods
### Insert (Method #1)
#### Code Pattern
```php
$db->Insert(string $tableName, array $input);
```
#### Example
```php  
$db->Insert('tablename', array('email' => "example@email.com"));
```
### Insert (Method #2)
#### Code Pattern
```php  
$db->Insert(string $tableName, array $inputNames, array $inputArray);
```
#### Example
```php  
$db->Insert('tablename', array('email'), array("example@email.com"));
```
### Insert (Method #3)
#### Code Pattern
```php  
$db->Insert(string $tableName, array $inputNames, array $inputArray);
```
#### Example
```php  
$db->Insert('tablename', array('email'), array(array("example@email.com"),array("example2@email.com")));
```

### Update
#### Code Pattern
```php  
$db->Update(string $tableName, array $where = null, array $input = null) {
```
#### Example
```php  
$db->Update('tablename', array('email' => "example@email.com"), array('email' => ""));
```

### Delete
#### Code Pattern
```php  
$db->Delete(string $tableName, array where = null);
```
#### Example
```php  
$db->Delete('tablename', array('email' => "example@email.com"));
```

### Select
#### Code Pattern
```php  
$db->Select(string $table, string/array $cells = null, array $where = null, boolean/int $limit = false, array/string $orderby = false, boolean $asc = true);
```
#### Example #1
```php  
$db->Select('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1"), 1, "id", true));
```
#### Example #2
```php  
$db->Select('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1"), 1, array("id" => true)));
```
### Select One
#### Code Pattern
```php  
$db->SelectOne(string $table, string/array $cells = null, array $where = null, array/string $orderby = false, boolean $asc = true);
```
#### Example
```php  
$db->SelectOne('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1")));
```
### Select Distinct
#### Code Pattern
```php  
$db->SelectDistinct(string $table, string/array $cells = null, array $where = null, boolean/int $limit = false, array/string $orderby = false, boolean $asc = true);
```
#### Example
```php  
$db->SelectDistinct('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1")));
```
### Select Distinct One
#### Code Pattern
```php  
$db->SelectDistinctOne(string $table, string/array $cells = null, array $where = null, array/string $orderby = false, boolean $asc = true);
```
#### Example
```php  
$db->SelectDistinctOne('user', array("name", "rights"), array("name" => "Dominic", array("!name" => "", "rights" => "1")));
```
### SelectCount
#### Code Pattern
```php  
$db->SelectCount(string TableName, array Where);
```
#### Example
```php  
$db->SelectCount('tablename', array('email' => "example@email.com"));
```

## Static Methods

### NOW
#### Default
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

### In
```php
$where = array('id' => array(1,2)); // id IN ('1', '2')
```
### Not In
```php
$where = array('!id' => array(1,2)); // id NOT IN ('1', '2')
```
### Between
```php
$where = array('~id' => array(1,2)); // id BETWEEN ('1', '2')
```
### Not Between
```php
$where = array('?id' => array(1,2)); // id BETWEEN ('1', '2')
```
### Not Between
```php
$where = array('!~id' => array(1,2)); // id BETWEEN ('1', '2')
```
### Other Statement
```php
$where = array(new DatabaseStatement('id = ?', array(1))) // id = '1'
```
### Database Function
```php
$where = array('date' => new DatabaseFunc('NOW()')) // data = NOW()


### and
```php  
$where = array("id" => "1", "name" => "Dominic"); // id = '1' AND name = 'Dominic'
```
### and or
```php  
$where = array("name" => "Dominic", array("id" => "1", "id" => "2")); // name = 'Dominic' AND (id = '1' OR id = '2')
```
### and or and
```php  
$where = array("name" => "Dominic", array("id" => "1", array("id" => "2", "key" => 1))); // name = 'Dominic' AND (id = '1' OR (id = '2' AND key = '1'))
```


## Select Column
### Unescaped column
```php
new DatabaseColumn('COUNT(*)') // COUNT (*)
```
