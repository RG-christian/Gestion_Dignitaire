param([ValidateSet('local','docker')][string]$Mode = 'local')
$Root = $PSScriptRoot
$Backend = Join-Path $Root 'backend'
$Frontend = Join-Path $Root 'frontend'

if ($Mode -eq 'local') {
    $mailpit = 'C:\tools\mailpit\mailpit.exe'
    if ((Test-Path $mailpit) -and -not (Get-Process mailpit -ErrorAction SilentlyContinue)) {
        Start-Job -Name GestionDignitaireMailpit -ScriptBlock {
            & 'C:\tools\mailpit\mailpit.exe' --smtp 127.0.0.1:1027 --listen 127.0.0.1:8027
        } | Out-Null
    }
    Start-Job -Name GestionDignitaireBackend -ScriptBlock {
        param($Path)
        Set-Location $Path
        php artisan serve --host=127.0.0.1 --port=8003
    } -ArgumentList $Backend | Out-Null
    Start-Job -Name GestionDignitaireFrontend -ScriptBlock {
        param($Path)
        Set-Location $Path
        npm run dev -- --port 3003
    } -ArgumentList $Frontend | Out-Null
    Write-Host 'Local: frontend http://localhost:3003 | backend http://localhost:8003 | Mailpit http://localhost:8027' -ForegroundColor Green
    Write-Host 'Suivi: Get-Job GestionDignitaire* | Receive-Job GestionDignitaireBackend' -ForegroundColor DarkGray
    exit 0
}

Set-Location $Root
docker compose up -d
if ($LASTEXITCODE -ne 0) { throw 'Le demarrage Docker a echoue.' }
docker compose ps
Write-Host 'Docker: frontend http://localhost:3002 | backend http://localhost:8000 | Mailpit http://localhost:8025' -ForegroundColor Green
