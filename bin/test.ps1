# set __DIR__=$(cd `dirname $0`; pwd);

$__DIR__ = $(Split-Path -Parent $MyInvocation.MyCommand.Definition)

echo $__DIR__

php -v

Start-Process php $__DIR__/../test.php > $__DIR__/../cli.log
