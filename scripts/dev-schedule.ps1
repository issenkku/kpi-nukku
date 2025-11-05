Param(
    [string]$Host = "127.0.0.1",
    [int]$Port = 8000
)

$ErrorActionPreference = "Stop"

$root = Resolve-Path (Join-Path $PSScriptRoot '..')
Set-Location $root

# Start the Laravel scheduler in a separate PowerShell window
Start-Process -FilePath "powershell" `
    -ArgumentList "-NoExit","-Command","Set-Location `"$root`"; php artisan schedule:work" `
    -WorkingDirectory $root `
    -WindowStyle Minimized `
    -Verb RunAs:$false

# Start the built-in PHP dev server (artisan serve) in the current window
php artisan serve --host $Host --port $Port

