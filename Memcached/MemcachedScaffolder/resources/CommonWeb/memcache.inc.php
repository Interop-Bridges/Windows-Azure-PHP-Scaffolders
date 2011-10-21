<?php
require_once $_SERVER["RoleRoot"] . '\\approot\\memcached-servers.php';
$memcache = new Memcache();
foreach ($memcachedServers as $memcachedServer) {
	if (strpos($memcachedServer[0], '127.') !== false) {
		$memcachedServer[0] = 'localhost';
	}
	$memcache->addServer($memcachedServer[0], $memcachedServer[1]);
}