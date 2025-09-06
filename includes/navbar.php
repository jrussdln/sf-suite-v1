<aside class="main-sidebar" >

    <!-- Brand Logo -->

    <a href="logout.php" class="brand-link" data-toggle="modal" data-action="user_logout">

        <img src="../dist/img/3.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">

        <span class="brand-text font-weight-light">Log out</span>

    </a>



    <a href="#" class="brand-link-card userProfile2-btn" data-id="<?php echo !empty($_SESSION['Identifier']) ? htmlspecialchars($_SESSION['Identifier']) : 'No Account'; ?>" data-toggle="modal" data-target="#userProfileModal">

        <i class="fas fa-user user-icon"></i>

        <div class="user-details">

            <div id="Identifier" class="Identifier">

                ID: <?php echo !empty($_SESSION['Identifier']) ? htmlspecialchars($_SESSION['Identifier']) : 'No Account'; ?>

            </div>

            <div class="access-level">

                <?php echo !empty($_SESSION['access_level']) ? htmlspecialchars($_SESSION['access_level']) : ''; ?>

            </div>

        </div>

    </a>

       <!-- School Logo (Centered in the Sidebar) -->

       <div class="sidebar-logo-container">

        <img src="../dist/img/3.png" alt="School Logo" class="school-logo" />

    </div>



    <div class="brand-text font-weight-light">

    <?php

        if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'LMP'||$_SESSION['access_level'] == 'HNP') {

    ?>

        <a href="main_dashboard.php" class="brand-link">

            <i class="fas fa-tachometer-alt"></i>

            <span class="brand-text font-weight-light">Dashboard</span>

        </a>

    <?php

        }

    ?>

    <?php

        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {

    ?>

        <a href="tracer.php" class="brand-link">

            <i class="fas fa-user-graduate"></i>

            <span class="brand-text font-weight-light">Alumni Tracer</span>

        </a>

    <?php

        }

    ?>

    <?php

        if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'LMP'||$_SESSION['access_level'] == 'HNP' ||$_SESSION['access_level'] == 'STUDENT') {

    ?>

        <a href="student_analytics.php" class="brand-link">

            <i class="fas fa-poll"></i>

            <span class="brand-text font-weight-light">Student Analytics</span>

        </a>

    <?php

        }

    ?>

    <?php

        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC') {

    ?>

        <a href="curriculum.php" class="brand-link">

            <i class="fas fa-cogs"></i>

            <span class="brand-text font-weight-light">Academic Structure</span>

        </a>

    <?php

        }

    ?>

    <?php

        if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'HNP' || $_SESSION['access_level'] == 'LMP') {

    ?>

        <a href="school_forms.php" class="brand-link">

            <i class="fas fa-file-alt"></i>

            <span class="brand-text font-weight-light">School Reports</span>

        </a>

    <?php

        }

    ?>

    <?php

        if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' ) {

    ?>

        <div class="nav-item">

        <a class="nav-link brand-text" data-toggle="collapse" href="#academicSetupMenu" role="button" aria-expanded="false" aria-controls="academicSetupMenu">

            <i class="fas fa-cogs"></i>

            <span>Academics</span>

            <i class="fas fa-angle-down float-right" id="sidebarToggleIcon1"></i>

        </a>

        <div class="collapse" id="academicSetupMenu">

            <ul class="nav flex-column pl-3">

        <?php

            if ($_SESSION['access_level'] == 'TEACHER' || $_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' ) {

        ?>

                <li class="nav-item">

                    <a class="brand-link-item" href="school_section.php">

                        <i class="fas fa-angle-right"></i> Sections

                    </a>

                </li>

        <?php

        }

            ?>

        <?php

            if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' ) {

        ?>

                <li class="nav-item">

                    <a class="brand-link-item" href="school_subject.php">

                        <i class="fas fa-angle-right"></i> Subjects

                    </a>

                </li>

                <li class="nav-item">

                    <a class="brand-link-item" href="school_student.php">

                        <i class="fas fa-angle-right"></i> Students

                    </a>

                </li>

                <li class="nav-item">

                    <a class="brand-link-item" href="school_personnel.php">

                        <i class="fas fa-angle-right"></i> Teachers

                    </a>

                </li>

            <?php

        }

            ?>

            </ul>

        </div>

    </div>

    <?php

        }

    ?>

    <?php

        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'TEACHER'||$_SESSION['access_level'] == 'LMP'||$_SESSION['access_level'] == 'HNP') {

    ?>

    <div class="nav-item">

        <a class="nav-link brand-text" data-toggle="collapse" href="#manageRecordsMenu" role="button" aria-expanded="false" aria-controls="manageRecordsMenu">

            <i class="fas fa-clipboard-list"></i>

            <span>Records</span>

            <i class="fas fa-angle-down float-right" id="sidebarToggleIcon2"></i>

        </a>

        <div class="collapse" id="manageRecordsMenu">

            <ul class="nav flex-column pl-3">

            <?php

                if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'TEACHER') {

            ?>

                <li class="nav-item">

                    <a class="brand-link-item" href="student_attendance.php">

                        <i class="fas fa-angle-right"></i> Attendance

                    </a>

                </li>

                <li class="nav-item">

                    <a class="brand-link-item" href="grades.php">

                        <i class="fas fa-angle-right"></i> Grade Records

                    </a>

                </li>

            <?php

                }

            ?>

            <?php

                if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'LMP') {

            ?>

                <li class="nav-item">

                    <a class="brand-link-item" href="learning_materials.php">

                        <i class="fas fa-angle-right"></i> Learning Materials

                    </a>

                </li>

            <?php

                }

            ?>

            <?php

                if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' || $_SESSION['access_level'] == 'HNP') {

            ?>

                <li class="nav-item">

                    <a class="brand-link-item" href="health_nutrition.php">

                        <i class="fas fa-angle-right"></i> Health and Nutrition

                    </a>

                </li>



            </ul>

            <?php

                }

            ?>

        </div>

    </div>

    <?php

        }

    ?>

    <?php

        if ($_SESSION['access_level'] == 'SA' || $_SESSION['access_level'] == 'SIC' ) {

    ?>



        <a href="user_accounts.php" class="brand-link">

            <i class="fas fa-users"></i>

            <span class="brand-text font-weight-light">User Administration</span>

        </a>

    <?php


        }

    ?>

    </div>





<!-- Footer Link -->

</aside>

