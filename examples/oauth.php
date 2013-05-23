<?

/*
---

An example of how to use the PHP kit for an oAuth call

---
*/

	ini_set('display_errors', 'on');

	// enter your app's config details here
	$oauthClient = 'sampleapp';
	$oauthSecret = '1234512345';
	$oauthCallback = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	// include class
	include('../Depot.php');
	
	// set up depot client
	$client = new Depot($oauthClient, $oauthSecret);
		
	// We need to build the authorise url and redirect user to authorise our app
	if (!$_GET['code']){
		    
	    $authoriseURL = $client->getAuthoriseURL($callbackURL);
	    
	    // redirect user
	    header("Location: ".$authoriseURL);
	    exit;
	    
	    
	// We now have the authorisation code to retrieve the access token
	} else {
	
	    $accessToken = $client->getAccessToken($_GET['code'], $callbackURL);
	    
	    echo '<pre>';
	    print_r($accessToken);
	    echo '</pre>';
	    
	    // or
	    
	    echo '<br>';
	    echo $accessToken['accessToken'];
	    
	    // Note: The access token does not expire so you can now store that access
	    // token in your database against that user or as a constant if you are
	    // talking with the api for your account only.
	}


?>