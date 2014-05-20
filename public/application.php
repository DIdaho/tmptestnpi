<link href="bower/bootstrap/dist/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="bower/font-awesome/css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="bower/angular-motion/dist/angular-motion.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="css/basic.css" media="screen" rel="stylesheet" type="text/css">

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

<script type="text/javascript" src="js/controller/npiList.js"></script>
<script type="text/javascript" src="js/controller/npiDetail.js"></script>
<script type="text/javascript" src="js/application.js"></script>

<div class="container-fluid" ng-app="Application">

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">

            <div class="navbar-header">
                <a class="navbar-brand" href="#">Apple NPI</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li ng-class="{active: activeRoute('npi')}"><a href="#/npi">NPI</a></li>
<!--                    <li ng-class="{active: activeRoute('/templates')}"><a href="#/templates">Configuration</a></li>-->
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

    <div ng-view></div>

</div>