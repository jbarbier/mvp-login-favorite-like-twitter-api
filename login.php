<?php

error_reporting(-1);
ini_set('display_errors', 'On');

session_start();
require_once('twitteroauth/twitteroauth.php');
include('config.php');

if(isset($_SESSION['name']) && isset($_SESSION['twitter_id'])) //check whether user already logged in with twitter
{

	echo "Name :".$_SESSION['name']."<br>";
	echo "Twitter ID :".$_SESSION['twitter_id']."<br>";
	echo "Image :<img src='".$_SESSION['image']."'/><br>";
	echo "<br/><a href='logout.php'>Logout</a>";

/////////////////////////////////////////////////////////////////////////////////////////

	print_r($_SESSION);
echo "<br />------------------------<br>";

	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET,
		      	   $_SESSION['request_token'],
			   $_SESSION['request_token_secret']);

	// get 5 tweets
	$statuses = $connection->get("search/tweets", ["q" => "holberton", "count" => 5]);

echo "<br />------------------------<br />\n";

//	print_r($statuses);

     $statuses = $statuses->statuses;
     foreach ($statuses as $s)
     {
//	print_r($s);
	echo	"NEW TWEET TO FAV<br />\n";
	echo $s->id_str . "<br />\n";
	echo $s->text . "<br />\n";
	echo "<a href='https://www.twitter.com/statuses/" . $s->id_str . "' target='_blank'>link</a><br />\n";
	echo "<br />";
	$connection->post("favorites/create", ["id" => $s->id]);
     }
}
else // Not logged in
{

	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET);
	$request_token = $connection->getRequestToken($OAUTH_CALLBACK); //get Request Token

	if ($request_token)
	{
		$token = $request_token['oauth_token'];
		$_SESSION['request_token'] = $token ;
		$_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];

		switch ($connection->http_code) 
		{
			case 200:
				$url = $connection->getAuthorizeURL($token);
				//redirect to Twitter .
		    	header('Location: ' . $url); 
			    break;
			default:
			    echo "Coonection with twitter Failed";
		    	break;
		}

	}
	else //error receiving request token
	{
		echo "Error Receiving Request Token";
	}

}



?>