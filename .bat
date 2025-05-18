@echo off
set TIMESTAMP=%DATE:~10,4%%DATE:~4,2%%DATE:~7,2%_%TIME:~0,2%%TIME:~3,2%%TIME:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%
set BACKUP_PATH=D:\Backups
set MYSQLDUMP_PATH=C:\xampp\mysql\bin\mysqldump.exe
set DB_USER=root
set DB_NAME=norea_psms

REM If you have a password, add -pYourPassword (no space) after -u %DB_USER%
"%MYSQLDUMP_PATH%" -u %DB_USER% --no-create-info %DB_NAME% > "%BACKUP_PATH%\\norea_psms-data-only-%TIMESTAMP%.sql"



# backup_norea_psms.ps1
if (!(Test-Path -Path "D:\Backups")) { New-Item -ItemType Directory -Path "D:\Backups" }
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupPath = "D:\Backups\norea_psms-backup-$timestamp.sql"
& "C:\xampp\mysql\bin\mysqldump.exe" -u root norea_psms | Out-File -Encoding utf8 $backupPath