$access_token = <OAUTH TOKEN>;

$device_id = <UNIQUE ID>;

$base_url = "https://developer-api.nest.com";
$path = '/devices/thermostats/'.$device_id.'/target_temperature_f';
$auth = '/?auth=' . $access_token;
$url = $base_url . $path . $auth;

$context = [
  'http' => [
    'method' => 'PUT',
    'follow_location' => true,
    'content' => '70'
  ]
];

$result = file_get_contents($url, false, $context);
