<head>
    <meta name="viewport"  content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link href="bower/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="bower/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="bower/angular-motion/dist/angular-motion.min.css" rel="stylesheet" type="text/css">
    <link href="css/basic.css" rel="stylesheet" type="text/css">
    <link href="css/mobile.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="bower/underscore/underscore.js"></script>

    <script type="text/javascript" src="bower/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="bower/jquery-ui/ui/minified/jquery-ui.min.js"></script>

    <script type="text/javascript" src="bower/angular/angular.js"></script>
    <script type="text/javascript" src="bower/angular-strap/dist/angular-strap.js"></script>
    <script type="text/javascript" src="bower/angular-strap/dist/angular-strap.tpl.min.js"></script>
    <script type="text/javascript" src="bower/angular-promise-tracker/promise-tracker.js"></script>
    <script type="text/javascript" src="bower/angular-route/angular-route.min.js"></script>
    <script type="text/javascript" src="bower/angular-sanitize/angular-sanitize.min.js"></script>
    <script type="text/javascript" src="bower/angular-ui-sortable/sortable.js"></script>
    <script type="text/javascript" src="bower/angular-growl/build/angular-growl.min.js"></script>

<!--    <script type="text/javascript" src="bower/bootstrap/dist/js/bootstrap.min.js"></script>-->

    <!-- CONTROLLERS -->
    <script type="text/javascript" src="js/controller/mobile.js"></script>

    <script type="text/javascript" src="js/application.js"></script>
    <script type="text/javascript" src="js/route/mobile.js"></script>
</head>

<body ng-app="Application">
    <div growl></div>
    <div ng-view></div>
</body>