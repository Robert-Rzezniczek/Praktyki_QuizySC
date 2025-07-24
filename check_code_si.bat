@echo off
setlocal ENABLEEXTENSIONS

set "RESULT_FILE=check_code.result.cache"
del /F /Q "%RESULT_FILE%"
type nul > "%RESULT_FILE%"

echo Running php-cbf... >> "%RESULT_FILE%" 2>&1
php vendor\bin\phpcbf --standard=Symfony src\ --ignore=Kernel.php >> "%RESULT_FILE%" 2>&1

echo Running php-cs-fixer... >> "%RESULT_FILE%" 2>&1
php vendor\bin\php-cs-fixer fix src\ --rules=@Symfony,@PSR1,@PSR2,@PSR12 --dry-run -vvv >> "%RESULT_FILE%" 2>&1

echo Running phpcs... >> "%RESULT_FILE%" 2>&1
php vendor\bin\phpcs --standard=Symfony src\ --ignore=Kernel.php >> "%RESULT_FILE%" 2>&1

@REM echo Running debug:translation (en)... >> "%RESULT_FILE%" 2>&1
@REM php bin/console debug:translation en --only-missing >> "%RESULT_FILE%" 2>&1

echo Running debug:translation (pl)... >> "%RESULT_FILE%" 2>&1
php bin/console debug:translation pl --only-missing >> "%RESULT_FILE%" 2>&1

endlocal
