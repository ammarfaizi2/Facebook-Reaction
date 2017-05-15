<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

define('data', __DIR__ . '/data/');
define('fb_data', __DIR__ . '/fb_data');
is_dir(fb_data) or mkdir(fb_data);
is_dir(data) or mkdir(data);

use System\Facebook;
use Curl\CMCurl;

header("Content-type:application/json");
$fb = new Facebook($config['email'],$config['pass'],$config['user'],$config['token']);
#print $fb->login();

interface mgr
{
}
$a = new class($config['token'],$config['user']) implements mgr 
{
	public function __construct($token,$user)
	{
		$this->token = $token;
		$this->user = $user;
		is_dir(data.$user) or mkdir(data.$user);
		file_exists(data.$user) ? null : file_put_contents(data.$user.'/target.txt', '');
	}
	public function get_new_post($id='me'): string {
		$ch = new CMCurl('https://graph.facebook.com/'.$id.'/feed?limit=1&fields=id&access_token='.$this->token);
		$dt = json_decode($ch->execute(),1) xor $ch->close();
		return isset($dt['data'][0]['id']) ? substr($dt['data'][0]['id'],strpos($dt['data'][0]['id'], '_')+1) : false;
	}	
	public function get_target()
	{
		$a = explode("\n", file_get_contents(data.$this->user.'/target.txt'));
		$d = array();
		foreach ($a as $val) {
			$b = explode("=", $val);
			$d[$b[0]] = explode(',', $b[1]);
		}
		return $d;
	}
	public function get_data()
	{
		return file_exists(data.$this->user.'/data.txt') ? json_decode(file_get_contents(data.$this->user.'/data.txt'),1) : array();
	}
	public function save($data)
	{
		return file_put_contents(data.$this->user.'/data.txt', json_encode($data,128));
	}
};
$data = $a->get_data(); 
foreach ($a->get_target() as $key => $value) {
	$cur_post = $a->get_new_post($key);
	if(!isset($data[$key][$cur_post])){
		$react = $value[rand(0,count($value)-1)];
		$fb->reaction($cur_post,$react);
		$data[$key][$cur_post] = array("reaction"=>$react,"date_time"=>date("Y-m-d H:i:s"));
	}
}
$a->save($data);


json_encode($data);





