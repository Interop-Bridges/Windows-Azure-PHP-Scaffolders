[Reflection.Assembly]::LoadWithPartialName("Microsoft.WindowsAzure.ServiceRuntime")

# Dump all memcached endpoints to ../memcached-servers.php
$memcached = "<?php`r`n"
$memcached += "`$memcachedServers = array("

$currentRolename = [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Role.Name
$roles = [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::Roles
foreach ($role in $roles.Keys | sort-object) {
	if ($role -eq $currentRolename) {
		$instances = $roles[$role].Instances
		for ($i = 0; $i -lt $instances.Count; $i++) {
			$endpoints = $instances[$i].InstanceEndpoints
			foreach ($endpoint in $endpoints.Keys | sort-object) {
				if ($endpoint -eq "MemcachedEndpoint") {
					$memcached += "array(`""
					$memcached += $endpoints[$endpoint].IPEndpoint.Address
					$memcached += "`" ,"
					$memcached += $endpoints[$endpoint].IPEndpoint.Port
					$memcached += "), "
				}


			}
		}
	}
}

$memcached += ");"

Write-Output $memcached | Out-File -Encoding Ascii ../memcached-servers.php

# Start memcached. To infinity and beyond!
while (1) {
	$p = [diagnostics.process]::Start("memcached.exe", "-m 64 -p " + [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.InstanceEndpoints["MemcachedEndpoint"].IPEndpoint.Port)
	$p.WaitForExit()
}