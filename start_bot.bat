@echo off
cd /d C:\OSPanel6\home\whether-bot
:loop
php index.php
timeout /T 5 /NOBREAK >nul
goto loop