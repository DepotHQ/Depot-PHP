<?

/*
---

An example of how to use the PHP kit for an oAuth call
This allows you to login as a specific user

---
*/

	ini_set('display_errors', 'on');

	// enter your app's config details here
	$oauthClient = 'your-client-id';
	$oauthSecret = 'your-client-secret';
	$oauthCallback = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

	// include class
	include('../Depot.php');
	
	// set up depot client
	$client = new Depot($oauthClient, $oauthSecret, 'oauth');
		
	// We need to build the authorise url and redirect user to authorise our app
	if (!isset($_GET['code'])){
		    
	    $authoriseURL = $client->getAuthoriseURL($oauthCallback);

	    // redirect user
	    header("Location: ".$authoriseURL);
	    exit;
	    
	    
	// We now have the authorisation code to retrieve the access token
	} else {
	
	    $accessToken = $client->getAccessToken($_GET['code'], $oauthCallback);
	    
	    echo '
	    	<p>It worked - your access token is: '.$accessToken->access_token.'</p>
	    	<p>Lets test the API by pulling down contacts</p>	
	    	<pre>';
	    
	    print_r($client->get('contacts'));
	    
	    echo '
	    	</pre>
	    ';
	    	    
	}


?>