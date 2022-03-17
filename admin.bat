@echo off
rem @since PM (25.01.2021) this script is used to manage the composer module
rem composer self-update
rem composer self-update --rollback

rem move to the directory in which the file currently resides
rem @see https://stackoverflow.com/a/5811989/2652918
rem echo COMMAND: %CD:~0,2% && CD "%~dp0%" && ...

title taPMeppe solutions: composer
set "LOCK=%~dp0%composer.lock"

rem TRUE if synchronised (autoload) only
if "%1%" EQU "sync" (
	goto :autoload
)
if "%1%" EQU "auto" (
	goto :autoload
)

rem echo %LOCK%
IF EXIST "%LOCK%" (
	rem TRUE if the composer lock has been found
	rem update
	rem @see https://getcomposer.org/doc/01-basic-usage.md#updating-dependencies-to-their-latest-versions
	echo UPDATE
	%CD:~0,2% && CD "%~dp0%" && composer update && composer dumpautoload -o
	goto :eof
) ELSE (
	rem TRUE if composer has not (properly) been installed
	rem install
	rem @see https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies
	echo INSTALLATION
	%CD:~0,2% && CD "%~dp0%" && composer install && composer dumpautoload -o
	goto :eof
)

:autoload
rem autoload
rem @see https://getcomposer.org/doc/01-basic-usage.md#autoloading
rem @see https://phpenthusiast.com/blog/how-to-autoload-with-composer
echo SYNCHRONISATION
%CD:~0,2% && CD "%~dp0%" && composer dumpautoload -o
