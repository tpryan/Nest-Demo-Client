#Nest Demo Client


This is a Nest demo client intended to run on a Google App Engine instance of PHP. It should work on a nonGAE version, but all testing has occured there. To use it you need the following:

* Nest Developer Account via the [Nest Developer Program](https://developer.nest.com/documentation)
* A defined Nest Client via the [Nest Developer Program](https://developer.nest.com/documentation)
* A file named config/settings.php which is ignored in this project.

In settings.php you need the following variables: 
* $client_id - Nest Client ID
* $client_secret - Nest Client Secret
* $token_url - Nest Authorization URL

