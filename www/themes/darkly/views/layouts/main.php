<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $this->pageTitle ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?= Yii::app()->theme->baseUrl ?>/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="<?= Yii::app()->theme->baseUrl ?>/css/font-awesome.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?= Yii::app()->theme->baseUrl ?>/css/theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div class="container">
    <div class="header clearfix">
        <h3 class="text-muted"><?= Yii::app()->name ?></h3>
    </div>

    <?= $content ?>

    <footer class="footer">
        <p><?= date('Y-m-d H:i:s') ?>.<?= round((microtime(true)-time())*1000) ?></p>
    </footer>

</div> <!-- /container -->


</body>
</html>
