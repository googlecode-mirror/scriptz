全文分为三个部分：

  1. 概述。PHP进行Web Services开发的优点，在Unix系统上安装配置PHP
  1. 在PHP中使用SOAP。NuSOAP 工具包，NuSOAP的高级Web Service功能，如HTTP代理，SOAP over HTTPS，document style messaging。还将讨论如何解决一些PHP Web Services编程将会遇到的问题，如安全问题，语言到数据类型的映射
  1. PHP中的XML-RPC。XML-RPC的特性，XML-RPC与SOAP的对比，然后使用Useful, Inc.实现来创建XML-RPC的客户端和服务器程序

'下面是第一部分'。

## Section 1. 概述 ##

PHP 中已经通过绑定了Expat parser内置了XML支持，额外的还可以使用一些扩展程序(extension)，如domxml(通过使用libxml库提供DOM, Xpath, Xlink支持)，xslt(为复杂的第三方XSLT库如Sablotron和libxslt提供的外包程序)。

另一个对 Web Service 开发有用的PHP扩展程序是CURL(Client URL Library)。CURL允许你通过不同的协议，如HTTP, HTTPS, FTP, telnet, LDAP来通讯，其中的HTTPS对Web Services与服务器进行安全连接尤其有用。

### SOAP vs XML-RPC 优缺点 ###

  * 强大的类型扩展 (SOAP)
  * 用户自定义字符集,如US-ASCII, UTF-8, UTF-16 (SOAP)
  * Specifies recipient [指定容器?] (SOAP)
  * 容器遇到无法理解的报文则失败 (SOAP)
  * 易于使用 (XML-RPC)
  * 设计简单 (XML-RPC)

### 配置PHP ###

  * Apache: 为了让PHP作为Apache的模块方式运行，使用 --with-apxs选项编译，如 --with-apxs=/www/bin/apxs。[我现在使用的Apache2, 我编译的PHP使用的选项是--with-apxs2=/usr/sbin/apxs]
  * DOMXML: 可选功能，对解析XML文档十分有帮助。需要预先安装好libxml库(版本>=2.4.2)，编译时使用 --with-dom=DIR 选项(缺省DIR为/usr)
> > http://www.xmlsoft.org/downloads.html
> > libxml 2.6.4 - sources - 2.52 MB
  * XSLT: 可选功能，对转换XML资料为其他类型的文档有帮助。编译时使用 --enable-xslt --with-xslt-sablot 选项。必须预先安装Sablotron XSLT库(http://www.gingerall.com/)，(缺省DIR为/usr/lib 或者 /usr/local/lib)。
> > Sablotron 1.0.1 - sources - 470 kB
  * CURL: 如前所述，若提供SSL支持则是必须安装的。编译时使用 --with-curl=DIR 选项。也同样需要预先安装CURL库(版本>=7.0.2-beta)。 [我的PHP已经安装了。CURL Information: libcurl/7.10.7 OpenSSL/0.9.7c zlib/1.1.4]
## Section 2. SOAP ##
### NuSOAP介绍 ###
> > NuSOAP是一组开源的，用来通过HTTP收发SOAP消息的PHP类，由NuSphere Corporation (http://www.nusphere.com
> > ) 开发。NuSOAP的一个优势是他不是一个扩展程序，而是纯粹用PHP代码写的，所以适用范围比较广。
> > 安装配置:
> > 从 http://dietrich.ganx4.com/nusoap/ 下载，从zip文件中解出nusoap.php文件放到include目录，在你的脚本前面加上
```
      include('nusoap.php');
```
> > 就搞定了。


> 范例：
> 下面是一个简单的SOAP client程序: soap\_client.php 执行
```
      <?php
      //simple client
      require('nusoap.php');

      //要发送的变量
      $myString="world";

      //parameters must be passed as an array
      //变量必须要转换成数组的形式
      $parameters=array($myString);

      //创建一个soapclient对象，参数是server的URL
      $s=new soapclient('http://www.douzi.org/me/php_ws/soap_server.php');

      //调用远程方法，返回值存放在$result
      //返回值为PHP的变量类型，如string, integer, array
      $result=$s->call('echoString', $parameters);

      //错误检测
      if (!$err=$s->getError()) {
      echo 'Result: '.$result; //success
      } else {
      echo 'Error: '.$err;
      }

      //调试，以下是SOAP请求(request)和回应(response)的报文，包括HTTP头
      echo "＜xmp＞".$s->request."＜/xmp＞";
      echo "＜xmp＞".$s->response."＜/xmp＞";

      ?>
```

> 相应的server端程序: soap\_server.php
```
      <?php
      //simple server
      require('nusoap.php');

      //创建一个新的soap_server对象，并注册允许远程调用的方法
      $s=new soap_server;
      $s->register('echoString');
      $s->register('echoArray');

      /*
      [文章中说: 缺少了注册这一步，任何PHP函数都将可以进行远程调用，这将是一个极大的安全隐患。但是我尝试过注册是必须的。而且只有将结果return的函数才能直接声明为远程方法，比如echo()就不行，而strtolower()就可以。]
      */

      function echoString($inputString) {
      //类性检查
      if(is_string($inputString)) {
      return "Hello, ".$inputString;
      } else {
      //soap_fault类用于产生错误信息
      return new soap_fault('client', '', 'The parameter to this service must be a string.');
      //soap_fault(faultcode, faultactor, faultstring, faultdetail);
      //上面是错误处理类的构造函数的格式
      //faultcode 必须值。可以设置为client或server，来表明错误发生在哪一端。
      //faultactor 在NuSOAP中尚未实现。
      //faultstring 错误信息。
      //faultdetail 详细错误信息。你可以使用XML标记。

      //除了构造函数外，soap_fault类还有一个serialize()方法
      //它将错误信息序列化，然后返回一个完整的SOAP报文，范例：
      /*
      $fault = new soap_fault('client', '', 'The inputString parameter must not be empty');
      echo $fault->serialize();
      */
      }
      }

      //演示数组类型的使用
      function echoArray($inputString) {
      return $inputString[0]."+".$inputString[1];

      }

      //最后一步是把所有的收到的post数据都传递给SOAP server的service方法。它将处理请求，并调用相应的函数。
      $s->service($HTTP_RAW_POST_DATA);
      ?>
```

> 复杂数据类型的使用:

  1. 数组。以下是生成的SOAP的Body部分代码:
```
            string1
            string2
```

> 2. 生成复合数据类型(compound types samples)，使用soapval。以下是生成的SOAP的Body部分代码:
```
            123 Freezing Lane
            Nome
            Alaska
            12345

            1234567890
            0987654321
```

> 程序范例: soapval.php 执行
```
            <?php
            //soapval: general compound types samples
            include('nusoap.php');

            $address=array(
            'street'=>'123 Freezing Lane',
            'city'=>'Nome',
            'state'=>'Alaska',
            'zip'=>12345,
            'phonenumbers'=>array('home'=>'1234567890', 'mobile'=>'0987654321')
            );

            $s=new soapval('myAddress', 'address', $address, '', 'http://myNamespace.com');

            print "＜xmp＞".$s->serialize()."＜/xmp＞";

            ?>

```

## WSDL ##
> WSDL是一种用于描述Web Service的XML语言。它是一种机读格式，把所有的访问服务所必须的信息提供给Web Service客户端。NuSOAP专门提供一个类进行WDSL文件的解析，并且从中提取信息。soapclient对象使用wsdl类来减轻开发者调用服务的难度。通过WSDL信息的帮助来创建报文，程序员仅仅需要知道操作的名字和参数就能调用它。

> 通过NuSOAP使用WSDL提供以下几点优点：
> > o 所有的服务元文件，如命名空间(namespaces)，endpoint URLs，参数名(parameter names)等等都可以直接从WSDL文件获得，这样就允许客户端动态的适应服务器端的变化。因为从服务器随时可以获得，所以这些数据不再需要在用户脚本中使用硬性编码。
> > o 它允许我们使用soap\_proxy类。这个类派生自soapclient类，增加了WDSL文件中详细列出的操作所对应的方法。现在用户通过它可以直接调用这些方法。
> > o soapclient 类包含一个getProxy()方法，它返回一个soap\_proxy类的一个对象。soap\_proxy类派生自soapclient类，增加了对应于 WSDL文档中定义的操作的方法，并且允许用户调用一个endpoint的远程方法。这仅仅适用于soapclient对象用WDSL文件初始化的情况。优点是易于用户使用，缺点是性能--PHP中创建对象是耗时的--且不为功利目的服务(and this functionality serves no utilitarian purpose)。


> 范例: wsdl.php 执行
```
      //wsdl的一个简单演示文件
      include('nusoap.php');

      //SOAP源为一个提供明星生卒年月的service
      //首先我们创建一个soapclient对象，把WSDL文件的URL传递给构造函数，
      //之后还要使用第二个参数以便使client知道我们传递过来的是WSDL，而不是SOAP endpoint。
      $s=new soapclient('http://www.abundanttech.com/webservices/deadoralive/deadoralive.wsdl', 'wsdl');

      //生成proxy类
      $p=$s->getProxy();

      //调用远程函数
      $sq=$p->getTodaysBirthdays();

      if (!$err=$p->getError()) {
      print_r($sq);
      } else {
      print "ERROR: $err";
      }

      print 'REQUEST:＜xmp＞'.$p->request.'＜/xmp＞';
      print 'RESPONSE:＜xmp＞'.str_replace('><', ">\n<", $p->response).'＜/xmp＞';

      ?>

```