<?php
function getActiveSchoolYear($pdo)
{
    $query = "SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active'";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return null;
    }
}
function getPreviousAcademicYear($activeYear)
{
    // Assuming the format is YYYY-YYY
    $years = explode('-', $activeYear);
    if (count($years) == 2) {
        return ($years[0] - 1) . '-' . ($years[1] - 1);
    }
    return null;
}
function count_enrolled_students($pdo, $school_year)
{
    $query = "
        SELECT COUNT(s.lrn) AS total_enrolled
        FROM student_tbl s
        JOIN school_year_tbl sy ON s.school_year = sy.sy_term
        WHERE sy.sy_term = :school_year
    ";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':school_year', $school_year);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_enrolled'] ?? 0;
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return 0;
    }
}
function calculateEnrollmentChange($current, $previous)
{
    if ($previous == 0 && $current == 0) {
        return ['value' => 0, 'direction' => 'no change'];
    } elseif ($previous == 0) {
        return ['value' => 100, 'direction' => '↑'];
    }

    $difference = abs($current - $previous);
    $larger = max($current, $previous);
    $percentage = ($difference / $larger) * 100;

    if ($current > $previous) {
        return ['value' => round($percentage, 2), 'direction' => '↑'];
    } elseif ($current < $previous) {
        return ['value' => round($percentage, 2), 'direction' => '↓'];
    } else {
        return ['value' => 0, 'direction' => 'no change'];
    }
}


function fetchEnrollmentData($pdo)
{
    $activeYear = getActiveSchoolYear($pdo);
    if (!$activeYear) {
        return ['error' => 'No active school year found.'];
    }

    $previousYear = getPreviousAcademicYear($activeYear);
    $currentEnrollment = count_enrolled_students($pdo, $activeYear);
    $previousEnrollment = count_enrolled_students($pdo, $previousYear);
    $percentageChange = calculateEnrollmentChange($currentEnrollment, $previousEnrollment);

    return [
        'total_enrolled' => $currentEnrollment,
        'previous_enrollment' => $previousEnrollment,
        'percentage_change' => $percentageChange['value'],
        'change_direction' => $percentageChange['direction']
    ];

}

function count_user($pdo)
{
    $query_active = "
        SELECT COUNT(user_status) AS total_active
        FROM user_tbl 
        WHERE user_status = 'Active'
    ";
    $query_total = "
        SELECT COUNT(*) AS total_users
        FROM user_tbl
    ";
    try {
        $stmt_active = $pdo->prepare($query_active);
        $stmt_active->execute();
        $result_active = $stmt_active->fetch(PDO::FETCH_ASSOC);
        $stmt_total = $pdo->prepare($query_total);
        $stmt_total->execute();
        $result_total = $stmt_total->fetch(PDO::FETCH_ASSOC);
        return [
            'total_active' => $result_active['total_active'] ?? 0,
            'total_users' => $result_total['total_users'] ?? 0
        ];
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return [
            'total_active' => 0,
            'total_users' => 0
        ];
    }
}
function count_personnel($pdo)
{
    $query = "SELECT COUNT(*) AS total_personnel FROM school_per_tbl";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_personnel'] ?? 0;
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return 0;
    }
}
function count_subjects($pdo)
{
    $query = "SELECT COUNT(*) AS total_subjects FROM subjects_tbl WHERE archive = 0";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_subjects'] ?? 0;
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return 0;
    }
}
function count_courses($pdo)
{
    $query = "
        SELECT COUNT(s.SectionId) AS total_courses
        FROM section_tbl s
        JOIN school_year_tbl sy ON s.SchoolYear = sy.sy_term
        WHERE sy.sy_status = 'Active'
    ";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_courses'] ?? 0;
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return 0;
    }
}
function count_teachers($pdo)
{
    $query = "
        SELECT COUNT(PersonnelId) AS total_teachers
        FROM school_per_tbl
    ";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_teachers'] ?? 0;
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return 0;
    }
}
function students_per_yearlevel($pdo)
{
    // Step 1: Retrieve the active school year term from school_year_tbl
    $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
    $stmt->execute();
    $activeYear = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$activeYear) {
        // No active school year found
        return [
            "year_levels" => [],
            "counts" => []
        ];
    }
    $sy_term = $activeYear['sy_term'];
    // Step 2: Query student_tbl for each distinct grade_level for the active school year and count the students
    $stmt = $pdo->prepare("
        SELECT grade_level, COUNT(id) AS count 
        FROM student_tbl 
        WHERE school_year = :sy_term 
        GROUP BY grade_level
        ORDER BY grade_level ASC
    ");
    $stmt->bindParam(':sy_term', $sy_term, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Prepare arrays for labels and counts
    $year_levels = [];
    $counts = [];
    foreach ($results as $row) {
        $year_levels[] = $row['grade_level'];
        $counts[] = (int) $row['count'];
    }
    return [
        "year_levels" => $year_levels,
        "counts" => $counts
    ];
}
// In your API endpoint, you can call this function as follows:
if (isset($_GET['students_per_yearlevel'])) {
    echo json_encode(students_per_yearlevel($pdo));
    exit;
}
function get_active_school_year($pdo)
{
    $stmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
    $stmt->execute();
    $activeSchoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
    return $activeSchoolYear ? $activeSchoolYear['sy_term'] : null;
}
function get_section_list($pdo)
{
    try {
        $syTerm = get_active_school_year($pdo);
        if (!$syTerm) {
            return ['error' => 'No active school year found.'];
        }
        $stmt = $pdo->prepare("SELECT SectionName FROM section_tbl WHERE SchoolYear = :syTerm");
        $stmt->execute([':syTerm' => $syTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}


function get_section_attendance($pdo)
{
    try {
        // Get active school year term
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);
        if (!$activeSchoolYear) {
            return [['error' => 'No active school year found.']];
        }
        $syTerm = $activeSchoolYear['sy_term'];

        // Get current day's column dynamically
        $currentDayColumn = "Day" . date('j');
        // Validate column name to prevent SQL injection
        if (!preg_match('/^Day([1-9]|[12][0-9]|3[01])$/', $currentDayColumn)) {
            return [['error' => 'Invalid day column detected']];
        }

        // Fetch all year levels
        $yearLevels = [7, 8, 9, 10, 11, 12];

        // Query to get attendance counts and "X" counts grouped by GradeLevel
        $query = "
            SELECT  
                s.GradeLevel,
                COALESCE(SUM(CASE WHEN (a.$currentDayColumn IS NULL OR a.$currentDayColumn = '' OR a.$currentDayColumn = 'L') THEN 1 ELSE 0 END), 0) AS attendance_count,
                COALESCE(SUM(CASE WHEN a.$currentDayColumn = 'X' THEN 1 ELSE 0 END), 0) AS x_count
            FROM section_tbl s
            LEFT JOIN attendance_tbl a 
                ON s.SectionId = a.section_id 
                AND a.attendance_term = :syTerm
            WHERE s.SchoolYear = :syTerm2
            AND (
                a.attendance_remarks IS NULL 
                OR TRIM(SUBSTRING_INDEX(a.attendance_remarks, ' ', 1)) NOT IN ('T/O', 'DRP')
            )
            GROUP BY s.GradeLevel;
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':syTerm', $syTerm, PDO::PARAM_STR);
        $stmt->bindValue(':syTerm2', $syTerm, PDO::PARAM_STR);
        $stmt->execute();
        $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map attendance data
        $attendanceMap = [];
        foreach ($attendanceData as $row) {
            $gradeLevel = (int) $row['GradeLevel'];
            $attendanceCount = (int) $row['attendance_count'];
            $xCount = (int) $row['x_count'];
            $totalCount = $attendanceCount + $xCount;

            // Calculate attendance percentage
            $attendancePercentage = ($totalCount > 0) ? round(($attendanceCount / $totalCount) * 100, 2) : 0;
            $attendanceMap[$gradeLevel] = [
                'attendance_count' => $attendanceCount,
                'x_count' => $xCount,
                'attendance_percentage' => $attendancePercentage
            ];
        }

        // Ensure every year level appears
        $finalResult = [];
        foreach ($yearLevels as $level) {
            $finalResult[] = [
                'GradeLevel' => $level,
                'attendance_count' => $attendanceMap[$level]['attendance_count'] ?? 0,
                'x_count' => $attendanceMap[$level]['x_count'] ?? 0,
                'attendance_percentage' => $attendanceMap[$level]['attendance_percentage'] ?? 0
            ];
        }

        return $finalResult;
    } catch (PDOException $e) {
        return [['error' => 'Database error: ' . $e->getMessage()]];
    }
}

function get_enrolled_counts($pdo)
{
    try {
        // Get active school year term
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);
        if (!$activeSchoolYear) {
            return [['error' => 'No active school year found.']];
        }
        $syTerm = $activeSchoolYear['sy_term'];
        // Fetch all sections
        $sectionsStmt = $pdo->prepare("
            SELECT TRIM(LOWER(SectionName)) AS SectionName 
            FROM section_tbl 
            WHERE SchoolYear = :syTerm
        ");
        $sectionsStmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $sectionsStmt->execute();
        $sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($sections)) {
            return [['error' => 'No sections found.']];
        }
        // Query to get enrolled student counts per section
        $query = "
            SELECT  
                TRIM(LOWER(s.SectionName)) AS SectionName, 
                COALESCE(COUNT(st.id), 0) AS enrollment_count
            FROM section_tbl s
            LEFT JOIN student_tbl st 
                ON TRIM(LOWER(s.SectionName)) = TRIM(LOWER(st.section)) 
                AND st.school_year = :syTerm
                AND (
                    st.remarks IS NULL 
                    OR TRIM(SUBSTRING_INDEX(st.remarks, ' ', 1)) NOT IN ('T/O', 'DRP')
                )
            WHERE s.SchoolYear = :syTerm2
            GROUP BY s.SectionName;
        ";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':syTerm', $syTerm, PDO::PARAM_STR);
        $stmt->bindValue(':syTerm2', $syTerm, PDO::PARAM_STR);
        $stmt->execute();
        $enrollmentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Map enrollment data
        $enrollmentMap = [];
        foreach ($enrollmentData as $row) {
            $enrollmentMap[$row['SectionName']] = (int) $row['enrollment_count'];
        }
        // Ensure every section appears in the result
        $finalResult = [];
        foreach ($sections as $section) {
            $sectionName = $section['SectionName'] ?? 'Unknown Section';
            $finalResult[] = [
                'SectionName' => $sectionName,
                'enrollment_count' => $enrollmentMap[$sectionName] ?? 0
            ];
        }
        return $finalResult;
    } catch (PDOException $e) {
        return [['error' => 'Database error: ' . $e->getMessage()]];
    }
}
function get_performance_counts($pdo)
{
    try {
        // Step 1: Get active school year term
        $syStmt = $pdo->prepare("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
        $syStmt->execute();
        $activeSchoolYear = $syStmt->fetch(PDO::FETCH_ASSOC);
        if (!$activeSchoolYear) {
            echo json_encode(["error" => "No active school year found."]);
            exit;
        }
        $syTerm = $activeSchoolYear['sy_term'];
        // Step 2: Fetch all sections for the active school year
        $sectionsStmt = $pdo->prepare("
            SELECT LOWER(TRIM(SectionName)) AS section_name 
            FROM section_tbl 
            WHERE SchoolYear = :syTerm
        ");
        $sectionsStmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $sectionsStmt->execute();
        $sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($sections)) {
            echo json_encode(["error" => "No sections found."]);
            exit;
        }
        // Step 3: Query to get student counts per section grouped by action_taken
        $query = "
            SELECT  
                LOWER(TRIM(section)) AS section_name, 
                action_taken, 
                COUNT(pa_id) AS student_count
            FROM prom_achievement_tbl
            WHERE school_year = :syTerm
                AND action_taken IN ('PROMOTED', 'CONDITIONAL', 'RETAINED')
                AND (remarks IS NULL OR remarks NOT IN ('T/O', 'DRP'))
            GROUP BY section, action_taken
        ";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':syTerm', $syTerm, PDO::PARAM_STR);
        $stmt->execute();
        $performanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Step 4: Organize data into a structured array
        $performanceMap = [];
        foreach ($performanceData as $row) {
            $sectionName = $row['section_name'];
            $action = strtoupper($row['action_taken']);
            $count = (int) $row['student_count'];
            if (!isset($performanceMap[$sectionName])) {
                $performanceMap[$sectionName] = [
                    'promoted' => 0,
                    'conditional' => 0,
                    'retained' => 0
                ];
            }
            if ($action === 'PROMOTED') {
                $performanceMap[$sectionName]['promoted'] = $count;
            } elseif ($action === 'CONDITIONAL') {
                $performanceMap[$sectionName]['conditional'] = $count;
            } elseif ($action === 'RETAINED') {
                $performanceMap[$sectionName]['retained'] = $count;
            }
        }
        // Step 5: Ensure all sections appear in the result with default values
        $finalResult = [];
        foreach ($sections as $section) {
            $sectionName = $section['section_name'];
            $finalResult[] = [
                'section_name' => $sectionName,
                'promoted' => $performanceMap[$sectionName]['promoted'] ?? 0,
                'conditional' => $performanceMap[$sectionName]['conditional'] ?? 0,
                'retained' => $performanceMap[$sectionName]['retained'] ?? 0
            ];
        }
        // Output JSON response
        echo json_encode($finalResult, JSON_PRETTY_PRINT);
        exit;
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        exit;
    }
}
function get_top_achievers($pdo, $section)
{
    // Get active school year
    $stmt = $pdo->query("SELECT sy_term FROM school_year_tbl WHERE sy_status = 'Active' LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $active_sy = $row['sy_term'] ?? null;
    if (!$active_sy || !$section)
        return [];
    $sql = "SELECT name, 
               general_average
        FROM prom_achievement_tbl 
        WHERE section = :section 
          AND school_year = :sy 
          AND general_average IS NOT NULL 
          AND general_average > 0
        ORDER BY general_average DESC 
        LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':section' => $section,
        ':sy' => $active_sy
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function get_accomplishment_data($pdo) {
    // Prepare response array
    $response = [
        'jhs' => ['total' => 0, 'valid' => 0],
        'shs' => ['total' => 0, 'valid' => 0]
    ];
    // Query for JHS (Grades 7–10)
    $jhsQuery = $pdo->query("
        SELECT 
            COUNT(DISTINCT lrn) AS total,
            COUNT(CASE WHEN general_average IS NOT NULL AND general_average <= 100 THEN 1 END) AS valid
        FROM prom_achievement_tbl
        WHERE grade_level BETWEEN 7 AND 10
    ");
    if ($jhsQuery) {
        $response['jhs'] = $jhsQuery->fetch(PDO::FETCH_ASSOC);
    }
    // Query for SHS (Grades 11–12)
    $shsQuery = $pdo->query("
        SELECT 
            COUNT(DISTINCT lrn) AS total,
            COUNT(CASE WHEN general_average IS NOT NULL AND general_average <= 100 THEN 1 END) AS valid
        FROM prom_achievement_tbl
        WHERE grade_level BETWEEN 11 AND 12
    ");
    if ($shsQuery) {
        $response['shs'] = $shsQuery->fetch(PDO::FETCH_ASSOC);
    }
    return $response;
}
