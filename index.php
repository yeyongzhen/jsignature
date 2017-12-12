<?php
header('Content-type:text/html;charset=utf-8');
$base64_image_content = $_POST['imgBase64'];
//匹配出图片的格式
if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
$type = $result[2];
$new_file = "upload/active/img/".date('Ymd',time())."/";
if(!file_exists($new_file))
{
//检查是否有该文件夹，如果没有就创建，并给予最高权限
mkdir($new_file, 0700);
}
$new_file = $new_file.time().".{$type}";
if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
echo '新文件保存成功：', $new_file;
}else{
echo '新文件保存失败';
}
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>手写板签名demo</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="UTF-8">
    <meta name="description" content="overview & stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
</head>
<body>
<div id="signature"></div>
<p style="text-align: center">
    <b style="color: red">请按着鼠标写字签名。</b>
</p>
<input type="button" value="保存" id="yes"/>
<input type="button" value="下载" id="download"/>
<input type="button" value="重写" id="reset"/>
<div id="someelement"></div>
<script src="./libs/jquery.js"></script>
<!--[if lt IE 9]>
<script src="./libs/flashcanvas.js"></script>
<![endif]-->
<script src="./libs/jSignature.min.js"></script>
<script>
    $(function() {
        var $sigdiv = $("#signature");
        $sigdiv.jSignature(); // 初始化jSignature插件.
        $("#yes").click(function(){
            //将画布内容转换为图片
            var datapair = $sigdiv.jSignature("getData", "image");
            var i = new Image();
            i.src = "data:" + datapair[0] + "," + datapair[1];
            $(i).appendTo($("#someelement")); // append the image (SVG) to DOM.
        });
        //datapair = $sigdiv.jSignature("getData","base30");
        //$sigdiv.jSignature("setData", "data:" + datapair.join(","));
        $("#download").click(function(){
            var base64img = $("img").attr("src");
            console.log(JSON.stringify(base64img));
            var bytes=window.atob(urlData.split(',')[1]);

            console.log(bytes);

            $.ajax({
                url:'index.php',
                type:'post',
                data:bytes
            });
//            downloadFile("a.png", convertBase64UrlToBlob(base64img));
        });
        $("#reset").click(function(){
            $sigdiv.jSignature("reset"); //重置画布，可以进行重新作画.
            $("#someelement").html("");
        });
    });
    function downloadFile(fileName, blob){
        var aLink = document.createElement('a');
        console.log(JSON.stringify(aLink));

        var evt = document.createEvent("HTMLEvents");
        console.log(JSON.stringify(evt));

        evt.initEvent("click", false, false);//initEvent 不加后两个参数在FF下会报错, 感谢 Barret Lee 的反馈

        aLink.download = fileName;

        console.log(JSON.stringify(URL.createObjectURL(blob)));
        aLink.href = URL.createObjectURL(blob);



        aLink.dispatchEvent(evt);


    }
    /**
     * 将以base64的图片url数据转换为Blob
     * @param urlData
     *            用url方式表示的base64图片数据
     */
    function convertBase64UrlToBlob(urlData){

        var bytes=window.atob(urlData.split(',')[1]);        //去掉url的头，并转换为byte

        //处理异常,将ascii码小于0的转换为大于0
        var ab = new ArrayBuffer(bytes.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < bytes.length; i++) {
            ia[i] = bytes.charCodeAt(i);
        }

        return new Blob( [ab] , {type : 'image/png'});
    }
</script>

</body>
</html>