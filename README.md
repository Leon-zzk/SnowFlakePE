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

可以使用提供的bench.php文件测试下扩展和使用php代码提供同样功能的性能差别(php bench.php),感觉微乎其微


有些同学安装PHP-CPP的问题，由于PHP7.1和7.0的版本没有zend_empty_string变量声明，安装php-cpp的时候会出现zend/classimpl.cpp:307:35: error: use of undeclared identifier 'zend_empty_string'这个错误，找到对应的文件，把zend_empty_string改成nullptr即可（php7.2以上的版本没有这个问题）
