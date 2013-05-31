<?

/*
---

An example of how to use the PHP kit for an account wide header call

---
*/

	ini_set('display_errors', 'on');

	// enter your app's config details here
	$depotSubdomain = 'demo';
	$apiKey = 'your-api-key';
	$apiSecret = 'your-api-secret';

	// include class
	include('../Depot.php');
	
	// set up depot client
	$client = new Depot($depotSubdomain, $apiKey, $apiSecret);
	
    echo '
    	<p>Lets test the API by pulling down contacts</p>	
    	<pre>';
    
    print_r($client->get('contacts'));
    
    echo '
    	</pre>
    ';

?>