[Reflection.Assembly]::LoadWithPartialName("Microsoft.WindowsAzure.ServiceRuntime")

$rdRoleId = [Environment]::GetEnvironmentVariable("RdRoleId", "Machine")

[Environment]::SetEnvironmentVariable("RdRoleId", [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Id, "Machine")
[Environment]::SetEnvironmentVariable("RoleName", [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Role.Name, "Machine")
[Environment]::SetEnvironmentVariable("RoleInstanceID", [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Id, "Machine")
[Environment]::SetEnvironmentVariable("RoleDeploymentID", [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::DeploymentId, "Machine")

if (![Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Id.Contains("deployment")) {
    if ($rdRoleId -ne [Microsoft.WindowsAzure.ServiceRuntime.RoleEnvironment]::CurrentRoleInstance.Id) {
        Restart-Computer
    }
}