# jsondb

If you want to save data with the schema as a JSON file, Check out this example.

```php
$db = new JsonDB(__DIR__ . '/db');

$db->insert('users', ['first_name' => 'Mohammad', 'last_name' => 'Norouzi', 'country' => 'Iran']); //  inserted to ./db/users.json

echo $db->select('users'); // Select what I inserted to ./db/users.json

echo $db->select('users' ,['first_name' => 'Mohammad', 'last_name' => 'Norouzi'] ); // Select * FROM USER WHERE FIRSTNAME = mohammad AND LASTNAME = norouzi

```

```php

$db->update('users', ['first_name' => 'Mohammad'], ['country' => 'USA']); // UPDATE TO users SET firstname = 'mohammad' WHERE country = 'usa'

echo $db->select('users'); // Show me, What I updated ./db/users.json


```

```php

$db->delete('users', ['first_name' => 'Mohammad']);

echo $db->select('users'); // Show me, What I deleted in ./db/users.json


```
