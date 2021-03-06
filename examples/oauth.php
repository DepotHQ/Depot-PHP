<?

/*
---

An example of how to use the PHP kit for an oAuth call
This allows you to login as a specific user

---
*/

	ini_set('display_errors', 'on');

	// enter your app's config details here
	$depotSubdomain = 'demo';
	$oauthClient = 'your-client-id';
	$oauthSecret = 'your-client-secret';
	$oauthCallback = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

	// include class
	include('../Depot.php');
	
	// set up depot client
	$client = new Depot($depotSubdomain, $oauthClient, $oauthSecret, 'oauth');
		
	// We need to build the authorise url and redirect user to authorise our app
	if (!isset($_GET['code'])){
	
		if (!isset($_GET['error'])){
			    
		    $authoriseURL = $client->getAuthoriseURL($oauthCallback);
	
		    // redirect user
		    header("Location: ".$authoriseURL);
		    exit;
	    
	    } else {
		    
		    echo '<p>Error</p><pre>'.$_GET['error'].'</pre>';
		    
	    }
	    
	    
	// We now have the authorisation code to retrieve the access token
	} else {
	
		// retrieve access token
	    $accessToken = $client->getAccessToken($_GET['code'], $oauthCallback);
	    
	    // set access token so it us used for future calls
	    $client->setAccessToken($accessToken);
	    
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