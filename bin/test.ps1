# set __DIR__=$(cd `dirname $0`; pwd);

$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

& $__DIR__\test2.ps1
# echo $__DIR__

# php -v

# Start-Process powershell "php $__DIR__/../test.php > $__DIR__/../cli.log"