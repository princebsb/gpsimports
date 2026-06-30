@echo off
:loop
ssh -p 443 -R0:127.0.0.1:80 -o StrictHostKeyChecking=no -o ServerAliveInterval=30 LsXALJKzv5b@pro.pinggy.io
timeout /t 10
goto loop