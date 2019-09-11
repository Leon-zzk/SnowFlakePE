# SnowFlakePE
依赖于PHP-CPP的一个snowflake算法，目前比较生硬，不怎么灵活，仅支持PHP7以上版本

感谢PHP-CPP让我等菜鸡也能写PHP扩展，由于本扩展依赖php-cpp库，所以需要先安装php-cpp，参考http://www.php-cpp.com/documentation/install

PS:应该有静态编译生成扩展的方式使得可以不再依赖系统预先装php-cpp，希望知道的看官不吝赐教。

如php-cpp官方所言，仅支持unix相关的系统，所以请在unix系统下使用此扩展

安装好php-cpp后，下载SnowFlakePE，解押进入其目录，直接make，然后make install即可，接着修改php.ini的文件填上extension=snowflake;

使用示例
$work = new \Cx\SnowFlake(23);//这个23可以是分布式服务的唯一id（snowflake算法里的workid），可以使用mysql表自增，或者zookeeper的选举id，只要不同即可，目前还未自动化，还需用户手动处理

for ($i = 0; $i < 100; $i++) {

    $id = $work->genId();
    
    echo $id . "<br>";
    
}

