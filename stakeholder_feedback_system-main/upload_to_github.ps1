# GitHub Upload Script for Feedback System
Write-Host "========================================" -ForegroundColor Green
Write-Host "    GitHub Upload Helper Script" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Set Git alias if not already set
if (-not (Get-Alias -Name git -ErrorAction SilentlyContinue)) {
    Set-Alias -Name git -Value "C:\Program Files\Git\bin\git.exe"
    Write-Host "Git alias set successfully!" -ForegroundColor Yellow
}

Write-Host "Step 1: Checking Git status..." -ForegroundColor Cyan
git status

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "    NEXT STEPS TO COMPLETE:" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "1. Go to https://github.com and create a new repository" -ForegroundColor White
Write-Host "2. Copy the repository URL (it will look like: https://github.com/username/repo-name.git)" -ForegroundColor White
Write-Host "3. Run these commands in your terminal:" -ForegroundColor White
Write-Host ""
Write-Host "   git remote add origin YOUR_REPOSITORY_URL" -ForegroundColor Yellow
Write-Host "   git branch -M main" -ForegroundColor Yellow
Write-Host "   git push -u origin main" -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "    Your files are ready for upload!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Press any key to continue..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
