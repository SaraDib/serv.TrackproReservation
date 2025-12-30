<#!
PowerShell MySQL dump script for TrackPro (Laravel backend)

Usage:
  - Run from PowerShell:  ./db_dump.ps1
  - Outputs: db_dump_<database>_<timestamp>.sql in current directory

Notes:
  - Reads DB settings from ../.env
  - Requires mysqldump.exe in PATH
#>

param(
  [string]$EnvPath = (Join-Path $PSScriptRoot "..\.env"),
  [string]$OutDir = $PSScriptRoot
)

function Parse-DotEnv {
  param([string]$Path)
  if (!(Test-Path -Path $Path)) {
    throw "Env file not found: $Path"
  }
  $map = @{}
  Get-Content -Path $Path | ForEach-Object {
    $line = $_.Trim()
    if ($line -eq '' -or $line.StartsWith('#')) { return }
    $eqIndex = $line.IndexOf('=')
    if ($eqIndex -lt 1) { return }
    $key = $line.Substring(0, $eqIndex).Trim()
    $val = $line.Substring($eqIndex + 1).Trim()
    # Remove surrounding quotes
    if ($val.StartsWith('"') -and $val.EndsWith('"')) {
      $val = $val.Substring(1, $val.Length - 2)
    }
    if ($val.StartsWith("'") -and $val.EndsWith("'")) {
      $val = $val.Substring(1, $val.Length - 2)
    }
    $map[$key] = $val
  }
  return $map
}

try {
  $envVars = Parse-DotEnv -Path $EnvPath
} catch {
  Write-Error $_
  exit 1
}

$conn = $envVars['DB_CONNECTION']
if (!$conn) { $conn = 'mysql' }

if ($conn -ne 'mysql') {
  Write-Error "DB_CONNECTION is '$conn'. This script supports MySQL only."
  Write-Host "Hint: Your local .env shows sqlite; run this on the server with MySQL."
  exit 1
}

$host = $envVars['DB_HOST']
$port = $envVars['DB_PORT']
$db   = $envVars['DB_DATABASE']
$user = $envVars['DB_USERNAME']
$pass = $envVars['DB_PASSWORD']

if (-not $host -or -not $db -or -not $user) {
  Write-Error "Missing DB settings in .env (need DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)."
  exit 1
}

if (-not $port) { $port = '3306' }

# Find mysqldump
$mysqldump = (Get-Command mysqldump.exe -ErrorAction SilentlyContinue)
if (-not $mysqldump) {
  $mysqldump = (Get-Command mysqldump -ErrorAction SilentlyContinue)
}
if (-not $mysqldump) {
  Write-Error "mysqldump not found in PATH. Install MySQL client or add it to PATH."
  exit 1
}

# Timestamp and output path
$ts = Get-Date -Format "yyyyMMdd_HHmmss"
if (!(Test-Path -Path $OutDir)) { New-Item -ItemType Directory -Path $OutDir | Out-Null }
$outfile = Join-Path $OutDir ("db_dump_${db}_$ts.sql")

Write-Host "Dumping MySQL database '$db' from $host:$port to '$outfile'..."

# Build argument list (use --result-file to avoid redirection issues)
$args = @(
  "--host=$host",
  "--port=$port",
  "--user=$user",
  "--password=$pass",
  "--default-character-set=utf8mb4",
  "--single-transaction",
  "--routines",
  "--triggers",
  "--events",
  $db,
  "--result-file=$outfile"
)

& $mysqldump.Source $args

if ($LASTEXITCODE -ne 0 -and $LASTEXITCODE -ne $null) {
  Write-Error "mysqldump failed with exit code $LASTEXITCODE"
  exit $LASTEXITCODE
}

if (Test-Path -Path $outfile) {
  $size = (Get-Item $outfile).Length
  Write-Host "Done. File size: $([Math]::Round($size/1KB,2)) KB"
} else {
  Write-Error "Dump file was not created."
  exit 1
}