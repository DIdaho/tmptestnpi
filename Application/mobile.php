<head>
    <meta name="viewport"  content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <?php include 'scripts.inc';?>

    <link href="css/mobile.css" rel="stylesheet" type="text/css">

    <!-- CONTROLLERS -->
    <script type="text/javascript" src="js/controller/mobile.js"></script>

    <script type="text/javascript" src="js/route/mobile.js"></script>
</head>

<body ng-app="Application">
    <div growl></div>
    <div ng-view></div>
</body>