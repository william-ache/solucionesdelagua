@echo off
echo Copiando archivos personalizados a laravel_temp
xcopy /s /e /y app\* laravel_temp\app\
xcopy /s /e /y database\* laravel_temp\database\
xcopy /s /e /y resources\* laravel_temp\resources\
xcopy /s /e /y routes\* laravel_temp\routes\

echo Borrando carpetas en la raiz
rmdir /s /q app
rmdir /s /q database
rmdir /s /q resources
rmdir /s /q routes

echo Moviendo todo a la raiz
xcopy /s /e /h /y laravel_temp\* .

echo Borrando laravel_temp
rmdir /s /q laravel_temp
