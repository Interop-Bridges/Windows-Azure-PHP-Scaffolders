[Reflection.Assembly]::LoadWithPartialName("Microsoft.WindowsAzure.ServiceRuntime")

# To infinity and beyond!

while(1) {

##########################################################
# Monitor environment to XML file
##########################################################

# Build some XML that may be useful to non-Microsoft languages running on Windows Azure
$reXml = "<?xml version=`"1.0`" encoding=`"UTF-8`" ?>`r`n"
$reXml += [System.String]::Format("<RoleEnvironment deploymentId=`"{0}`" isAvailable=`"{1}`" isEmulated=`"{2}`">`r`n", [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::DeploymentId,  [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::IsAvailable,  [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::IsEmulated)

$reXml += [System.String]::Format("  <CurrentRoleInstance id=`"{0}`" roleName=`"{1}`" updateDomain=`"{2}`" faultDomain=`"{2}`">`r`n", [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Id, [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Role.Name, [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.UpdateDomain, [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.FaultDomain)
$reXml += "    <Endpoints>`r`n"
$endpoints = [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.InstanceEndpoints
foreach ($endpoint in $endpoints.Keys) {
	$reXml += [System.String]::Format("      <Endpoint id=`"{0}`" protocol=`"{1}`" address=`"{2}`" port=`"{3}`" />`r`n", $endpoint, $endpoints[$endpoint].Protocol,  $endpoints[$endpoint].IPEndpoint.Address,  $endpoints[$endpoint].IPEndpoint.Port)
}
$reXml += "    </Endpoints>`r`n"
$reXml += "  </CurrentRoleInstance>`r`n"

$reXml += "  <Roles>`r`n"
$roles = [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::Roles
foreach ($role in $roles.Keys) {
	$reXml += [System.String]::Format("    <Role name=`"{0}`">`r`n", $role)
	$reXml += "      <Instances>`r`n"
	$instances = $roles[$role].Instances
	for ($i = 0; $i -lt $instances.Count; $i++) {
		$reXml += [System.String]::Format("        <RoleInstance id=`"{0}`" roleName=`"{1}`" updateDomain=`"{2}`" faultDomain=`"{2}`">`r`n", $instances[$i].Id, $instances[$i].Role.Name, $instances[$i].UpdateDomain, $instances[$i].FaultDomain)
		$reXml += "          <Endpoints>`r`n"
		$endpoints = $instances[$i].InstanceEndpoints
		foreach ($endpoint in $endpoints.Keys) {
			$reXml += [System.String]::Format("            <Endpoint id=`"{0}`" protocol=`"{1}`" address=`"{2}`" port=`"{3}`" />`r`n", $endpoint, $endpoints[$endpoint].Protocol,  $endpoints[$endpoint].IPEndpoint.Address,  $endpoints[$endpoint].IPEndpoint.Port)
		}
		$reXml += "          </Endpoints>`r`n"
		$reXml += "        </RoleInstance>`r`n"
	}
	$reXml += "      </Instances>`r`n"
	$reXml += "    </Role>`r`n"
}
$reXml += "  </Roles>`r`n"

$reXml += "</RoleEnvironment>`r`n"


Write-Output $reXml | Out-File -Encoding Ascii ../monitor-environment.xml


##########################################################
# Create memcached include file for PHP
##########################################################

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



# Restart the loop in 1 minute
Start-Sleep -Seconds 60
}