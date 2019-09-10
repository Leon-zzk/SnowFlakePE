# SnowFlakePE
依赖于PHP-CPP的一个snowflake算法，目前比较生硬，不怎么灵活

感谢PHP-CPP让我等菜鸟也能写PHP扩展，由于本扩展依赖php-cpp库，所以需要先安装php-cpp，参考http://www.php-cpp.com/documentation/install

PS:应该有静态编译生成的扩展可以不再依赖系统预先装php-cpp，希望知道的看官不吝赐教。

如php-cpp官方所言，仅支持unix相关的系统，所以请在unix系统下使用此扩展

安装好php-cpp后，下载SnowFlakePE，解押进入其目录，直接make，然后make install即可，接着修改php.ini的文件填上extension=snowflake;

使用示例
$work = new \SnowFlake(23);
for ($i = 0; $i < 100; $i++) {
    $id = $work->genId();
    echo $id . "<br>";
}
