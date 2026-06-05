@echo off
setlocal enabledelayedexpansion

set "backupDir=C:\laragon\www\ngonser.id\storage\backups"
set "mysqlDir=C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin"

:: Mengambil tanggal dan waktu secara universal yang aman untuk semua format laptop
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set "dt=%%I"
set "year=%dt:~0,4%"
set "month=%dt:~4,2%"
set "day=%dt:~6,2%"
set "hour=%dt:~8,2%"
set "minute=%dt:~10,2%"

:: Menyusun stempel waktu (timestamp) sesuai standar modul
set "timestamp=!year!-!month!-!day!_!hour!-!minute!"

:: Backup database
"%mysqlDir%\mysqldump" -u adm_backup -padmin123 ngonser_db > "%backupDir%\backup_%timestamp%.sql"

endlocal