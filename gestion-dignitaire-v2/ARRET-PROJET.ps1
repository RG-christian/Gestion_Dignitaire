param([ValidateSet('local','docker')][string]$Mode = 'local')
if ($Mode -eq 'docker') { Set-Location $PSScriptRoot; docker compose down; exit $LASTEXITCODE }
Get-Job GestionDignitaire* -ErrorAction SilentlyContinue | Stop-Job -PassThru | Remove-Job -Force

# Ne toucher qu'aux processus qui écoutent les ports de ce projet.
$portsProjet = @(3003, 8003, 1027)
foreach ($port in $portsProjet) {
    $connections = Get-NetTCPConnection -State Listen -LocalPort $port -ErrorAction SilentlyContinue
    foreach ($connection in $connections) {
        $process = Get-Process -Id $connection.OwningProcess -ErrorAction SilentlyContinue
        if ($process) {
            Stop-Process -Id $process.Id -Force
            Write-Host "Processus du port $port arrêté : $($process.ProcessName) (PID $($process.Id))" -ForegroundColor Yellow
        }
    }
}
Write-Host 'Seuls les processus des ports 3003, 8003 et 1027 ont été arrêtés.' -ForegroundColor Green
