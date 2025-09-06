<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SF-Suite: PVPMNHS</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php
    include_once('core_stylesheet.php');
  ?>
  
</head>
<body class="hold-transition sidebar-collapse">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark" style="background: -webkit-linear-gradient(to right, #281E5D, #1565C0, #b92b27); 
       background: linear-gradient(to right, #281E5D, #1565C0); 
       color: #ffffff;">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="main_dashboard.php" class="nav-link">Pedro V. Panaligan Memorial National High School</a>
      </li>
    </ul>

     <!-- Right corner EMPNO -->
<ul class="navbar-nav ml-auto">
<!-- Header Section -->
<li class="nav-item d-none d-sm-inline-block selected-school-year" style="display: flex; align-items: center; justify-content: center; height: 100%;">
    <span id="selectedSchoolYear" class="nav-link small-text" style="font-size: 12px; color: #fff; display: flex; align-items: center;">
        S.Y. <span id="schoolYearText" style="font-size: 12px; color: #fff; margin-left: 5px;">Loading...</span> 
        <span style="font-size: 14px; color: rgb(255, 255, 255); margin-left: 5px;">&#9662;</span>
    </span>
</li>

    <?php 
    if($_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'SA') {
    ?>   
        <!-- Announcements -->
        <li class="nav-item d-none d-sm-inline-block">
            <a href="manage_announcements.php" class="nav-link btn">
                <i class="fas fa-bell"></i>
            </a>
        </li>
    <?php 
    }
    ?>

    <!-- Refresh -->
    <li class="nav-item d-none d-sm-inline-block">
        <a href="" class="nav-link btn" id="refreshButton">
            <i class="fas fa-sync-alt"></i>
        </a>
    </li>

    <!-- User Profile -->
    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" 
            class="nav-link btn userProfile2-btn" 
            data-id="<?php echo !empty($_SESSION['Identifier']) ? htmlspecialchars($_SESSION['Identifier']) : 'No Account'; ?>" 
            data-toggle="modal" 
            data-target="#userProfileModal">
            <i class="fas fa-user mr-1"></i>
        </a>
    </li>
</ul>


    <!-- Right navbar links -->
    
  </nav>
