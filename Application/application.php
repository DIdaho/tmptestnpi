<?php include 'scripts.inc';?>

<title>Apple NPI</title>

<link href="css/application.css" rel="stylesheet" type="text/css">

<!-- CONTROLLERS -->
<script type="text/javascript" src="js/controller/list.js"></script>
<script type="text/javascript" src="js/controller/listNpi.js"></script>
<script type="text/javascript" src="js/controller/listWave.js"></script>
<script type="text/javascript" src="js/controller/listActivity.js"></script>
<script type="text/javascript" src="js/controller/listContact.js"></script>
<script type="text/javascript" src="js/controller/addPosToWave.js"></script>
<script type="text/javascript" src="js/controller/report.js"></script>
<script type="text/javascript" src="js/route/application.js"></script>

<div class="container-fluid" ng-app="Application">

    <script type="text/ng-template" id="template/pagination/pagination.html">
        <?php include '../public/bower/angular-ui-bootstrap/template/pagination/pagination.html';?>
    </script>

    <div growl></div>

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" ng-if="!activeRoute('mobile')">
        <div class="container-fluid">

            <div class="navbar-header">
                <a class="navbar-brand" href="#">Apple NPI</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li ng-class="{active: activeRoute('npi')}"><a href="#/npi"><i class="fa fa-globe fa-lg"></i> NPI</a></li>
                    <li ng-class="{active: activeRoute('contact')}"><a href="#/contact"><i class="fa fa-users fa-lg"></i> Contacts</a></li>
                    <li ng-class="{active: activeRoute('activity')}"><a href="#/activity"><i class="fa fa-folder fa-lg"></i> Activities</a></li>
<!--                    <li ng-class="{active: activeRoute('/templates')}"><a href="#/templates">Configuration</a></li>-->
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>

    <div class="loading-indicator progress progress-striped active" ng-show="tracker.active()"><div class="progress-bar progress-bar-info" style="width:100%"></div></div>

    <div ng-view></div>

</div>