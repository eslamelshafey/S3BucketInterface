<?php

require 'vendor/autoload.php';

require 'app.php';

if($_SERVER['REQUEST_METHOD'] == "POST") {

    function card($name, $path, $img=null) {
        $img = (isset($img) ? $img : 'https://ftmsa.s3.me-south-1.amazonaws.com/download/c/2.svg');
        return '<div class="card">
            <img src='.(isset($img) ? $img : 'https://ftmsa.s3.me-south-1.amazonaws.com/download/c/2.svg').'>
            <a href='.$path.'>'.$name.'</a>
        </div>';
    }

    $results = $s3->getPaginator('ListObjects', [
        'Bucket'=>$config['s3']['bucket'],
    ]);

    if(isset($_REQUEST['q']) && !empty($_REQUEST['q'])) {
        $results = $results->search("Contents[?starts_with(Key,'".$_REQUEST['q']."')]");
    } else {
        $results = $results->search("Contents[?starts_with(Key, '')]");
    }
    
    $ret = '';
    foreach ($results as $result) {
        $name = explode('/', $result['Key']);
        $name = (strlen($name[count($name)-1]) > 0 ? $name[count($name)-1] : $result['Key']);
        $path =$s3->getObjectUrl($config['s3']['bucket'], $result['Key']);
        $ret .= card($name, $path);
    }
    
    echo strlen($ret) > 0 ? $ret : card('لا يوجد ملف بهذا الاسم', '#', 'https://ftmsa.s3.me-south-1.amazonaws.com/download/close%281%29.svg');
    return;
}

$results = $s3->getPaginator('ListObjects', [
    'Bucket'=>$config['s3']['bucket'],
]);

if(isset($_GET['q']) && !empty($_GET['q'])) {
    $results = $results->search("Contents[?starts_with(Key,'".$_GET['q']."')]");
} else {
    $results = $results->search("Contents[?starts_with(Key, '')]");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        a{
            display:block;
            margin:2px 0;
        }
        #search{
            border-radius: 100px;
            border: solid #ddd 2px;
            padding: 5px 10px;
            margin: 0 auto;
            display: block;
            text-align:center;
        }
        .media{
            display: grid;
            grid-template-columns: auto auto auto auto;
            text-align: center;
            margin:0 auto;
            width:80%;
        }
        .media .card{
            width: 200px;
            height: max-content;
        }
        .media img{
            height:100%;
        }
        .loader{
            animation: loader 1s linear 0s infinite alternate;
            position:absolute;
            top:0;
            left:0;
            opacity: .6;
            width:100%;
            height:100%;
        }
        @keyframes loader {
            0%{
                background:#000;
            }
            100%{
                background:#fff;
            }
        }
    </style>
    <title>Title</title>
</head>
<body>
    <input id="search" name="search" placeholder="ادخل اسم الملف" type="text" onchange="">

    <div class="result">
        <h1 style="text-align:center">نتائج البحث</h1>
        <h3 style="text-align:center;color:red">اذا كانت الفيديوهات مرفوعه يتم استدعاء ملف الصورة الذي يحتوي علي علامه صح</h3>
        <div class="media">
            
            <?php foreach ($results as $result) { ?>
                <div class="card">
                    <?php 
                        $name = explode('/', $result['Key']);
                        $name = (strlen($name[count($name)-1]) > 0 ? $name[count($name)-1] : $result['Key']);
                        $path =$s3->getObjectUrl($config['s3']['bucket'], $result['Key']) ;
                    ?>
                    <img src="https://ftmsa.s3.me-south-1.amazonaws.com/download/c/2.svg">
                    <?php echo "<a href='".$path."'>".$name."</a>" . PHP_EOL; ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>

        function $(selector) {
            return document.querySelectorAll(selector);
        }

        function search(str, selector) {
            
            selector.style.cssText = "position:relative;";

            var loader = document.createElement("div");
            loader.classList = "loader";

            selector.appendChild(loader);

            fetch("<?php echo $config['BASE_URL']; ?>/1.php?q="+str, {
                method:"POST",
                body: JSON.stringify({"q":str}),
            })
            .then(res => res.text())
            .then(data => {
                selector.innerHTML = (data.length > 0 ? data : "<div style='color:red'>لا يوجد بيانات لعرضها</div>")
                loader.remove();
            })

        }

        function updateQueryString(str) {

            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?q=' + str;
                window.history.pushState({path:newurl},'',newurl);
            }

        }
        
        window.addEventListener("load", function() {

            $("#search")[0].addEventListener("keyup", function() {
                let str = this.value;
                updateQueryString(str);
                search(str, $(".media")[0]);
            });

        });

    </script>
</body>
</html>