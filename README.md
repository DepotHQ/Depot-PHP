# Depot-PHP 

A PHP toolkit for accessing the Depot HQ API.

--------------

### Installation

To install simply include the Depot.php file in your PHP script, and instantiate the class. 

```php
include('/path/to/Depot.php');
$depotClient = new Depot('my-subdomain', 'my-api-key', 'my-api-secret');
```

### Authentication
The toolkit supports two authentication methods, headers and OAuth.

#### Headers
Headers authentication gives you access to an entire Depot instance with no restrictions - so use this carefully! 

Headers require an API key and secret pair, which can be found in the account area of your Depot instance.

An example of usage can be found in the `examples/headers.php` file

#### OAuth
OAuth authentication allows you to take on the persona of a user on the system, and act on their behalf. In order to act as an OAuth client, you need an OAuth application id and secret - this can be provided by emailing oauth@depothq.com with details about your application.

One you have been supplied with these details, you need to authenticate the user against the authorise URL. The process is this:

1. You build the authorize URL and redirect the user
2. The user authenticates + grants permission
3. The user is redirected to your callback URL along with a temporary code
4. You authenticate that code with Depot to be given an access token that doesn't expire.
5. You use that access token to make future calls to the system

The good news is that the `examples/oauth.php` file contains code on how to do all this.

### Usage
Once you have authenticated you can begin using the toolkit to call the API.

#### GET requests
GET requests are made to get existing resources, and work as follows:

```php
$depotClient->get('resource_name', array('optional_param' => 'value'));
```

#### POST requests
POST requests are made to create new resources, and work as follows:

```php
$depotClient->post('resource_name', array('required_param' => 'value'));
```

#### PATCH requests
PATCH requests are made to update existing resources, and work as follows:

```php
$depotClient->patch('resource_name', array('param_to_update' => 'value'));
```

#### DELETE requests
DELETE requests are made to delete existing resources, and work as follows:

```php
$depotClient->delete('resource_name');
```


### Resources
The list of resources consumable by the API can be found at http://www.depothq.com/api

### Support
If you require help or further explanation, feel free to drop us an email help@depothq.com or message us on twitter (@depothq)