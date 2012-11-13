# **Relaxo**

Basic REST for FileMaker PHP API

Tim Culbert | October 2012

---

## **Config**

This has only been tested with FileMaker Server 11 but should work for FileMaker Server 12 as there has been no change to the PHP API.

Put Relaxo in your web publishing root eg. \inetpub\wwwroot\relaxo\. For Apache use the **.htaccess** file, for IIS use **web.config**.

Configure the location of the FileMaker PHP API in database.php.
```php
// Specify the FileMaker PHP API location
require_once('../fmi/FileMaker.php');
```
Create a dbconfig file by copying or modifying the **contacts.php** example included.
```php
// The database host, usually 127.0.0.1 or localhost
$hostname = '127.0.0.1';

// The name of the FileMaker database to connect to
$database = 'contacts';

// The alias to use when accessing the API, set to $database if no alias required
$alias = 'people';

// Database username and password for an account that has access via PHP Web Publishing
$username = 'web';
$password = 'web';

// The FileMaker layout configured for PHP
$layout = 'Web';

// The name of the unique identifier field from this database - leave blank to use FileMaker's recordId
$id = 'ContactID';
```

## **Testing**

Upload the **contacts.fp7** file (from /example/) to your FileMaker Server.

Open a web browser and access **http://localhost/relaxo/contacts/1**

This will perform a GET request which should return 

```
{"ContactID":"1","Firstname":"Tim","Lastname":"Culbert","Email":"tim@fake.email.com"}
```

The two provided dbconfig files both reference the provided example contacts.fp7 database.

```
people.php - /relaxo/people/ - Read Only
contacts.php - /relaxo/contacts/ - Read/Write
```

To test PUT, POST and DELETE requests you can use my REST test tool (link to come).