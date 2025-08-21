@echo off
echo ========================================
echo    GitHub Upload Helper Script
echo ========================================
echo.

echo Step 1: Checking if Git is available...
where git >nul 2>&1
if %errorlevel% neq 0 (
    echo Git not found in PATH. Please restart your terminal/command prompt.
    echo Or download Git from: https://git-scm.com/download/win
    pause
    exit /b 1
)

echo Git found! Proceeding with GitHub setup...
echo.

echo Step 2: Initializing Git repository...
git init

echo.
echo Step 3: Adding all files to Git...
git add .

echo.
echo Step 4: Making initial commit...
git commit -m "Initial commit: Complete feedback system with HTML, CSS, JavaScript, PHP, and MySQL"

echo.
echo ========================================
echo    NEXT STEPS TO COMPLETE:
echo ========================================
echo.
echo 1. Go to https://github.com and create a new repository
echo 2. Copy the repository URL (it will look like: https://github.com/username/repo-name.git)
echo 3. Run these commands in your terminal:
echo.
echo    git remote add origin YOUR_REPOSITORY_URL
echo    git branch -M main
echo    git push -u origin main
echo.
echo ========================================
echo    Your files are ready for upload!
echo ========================================
pause
