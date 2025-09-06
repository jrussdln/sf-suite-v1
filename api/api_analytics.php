<?php
require_once('../includes/sessions.php');
require_once('../includes/db_config.php');
header('Content-type: application/json');
// Get LRN from POST input (trim any extra whitespace)
$lrn = isset($_POST['lrn']) ? trim($_POST['lrn']) : '';
$timelineData = [];
if (!empty($lrn)) {
    // Get distinct school years from student_tbl
    $schoolYearQuery = "SELECT DISTINCT school_year FROM student_tbl WHERE lrn = :lrn ORDER BY school_year DESC";
    $stmt = $pdo->prepare($schoolYearQuery);
    $stmt->execute(['lrn' => $lrn]);
    $schoolYears = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($schoolYears) {
        foreach ($schoolYears as $row) {
            $schoolYear = $row['school_year'];
            // --- HEALTH DATA ---
            $healthQuery = "SELECT weight, height, bmi, created_at 
                            FROM health_nutrition_tbl 
                            WHERE lrn = :lrn AND hnr_term = :school_year";
            $stmtHealth = $pdo->prepare($healthQuery);
            $stmtHealth->execute(['lrn' => $lrn, 'school_year' => $schoolYear]);
            $healthData = $stmtHealth->fetch(PDO::FETCH_ASSOC) ?? [
                'weight' => 0,
                'height' => 0,
                'bmi' => 0,
                'created_at' => null
            ];
            // Fallback individually in case some fields exist but are null
            $healthData['weight'] = $healthData['weight'] ?? 0;
            $healthData['height'] = $healthData['height'] ?? 0;
            $healthData['bmi'] = $healthData['bmi'] ?? 0;
            $healthData['created_at'] = $healthData['created_at'] ?? null;
            // --- LEARNING MATERIALS ---
            $learningMaterialsQuery = "SELECT Desc1, Desc2, Desc3, Desc4, Desc5, Desc6, Desc7, Desc8,
Returned1, Returned2, Returned3, Returned4, Returned5, Returned6, Returned7, Returned8
FROM learning_material_tbl 
WHERE lrn = :lrn AND lm_term = :school_year";
            $stmtLM = $pdo->prepare($learningMaterialsQuery);
            $stmtLM->execute(['lrn' => $lrn, 'school_year' => $schoolYear]);
            $learningMaterialsData = $stmtLM->fetch(PDO::FETCH_ASSOC);
            $learningMaterials = '';
            if ($learningMaterialsData) {
                $descriptions = [];
                for ($i = 1; $i <= 8; $i++) {
                    $descKey = 'Desc' . $i;
                    $returnedKey = 'Returned' . $i;
                    if (!empty($learningMaterialsData[$descKey])) {
                        $status = !empty($learningMaterialsData[$returnedKey]) ? 'Returned' : 'Not Returned';
                        $descriptions[] = "{$learningMaterialsData[$descKey]} ({$status})";
                    }
                }
                $learningMaterials = implode(', ', $descriptions);
            }
            // --- GRADE LEVEL & SUBJECTS (General Average) ---
            // Get the student's grade level for this school year
            $gradeQuery = "SELECT grade_level FROM student_tbl WHERE lrn = :lrn AND school_year = :school_year";
            $stmtGrade = $pdo->prepare($gradeQuery);
            $stmtGrade->execute(['lrn' => $lrn, 'school_year' => $schoolYear]);
            $gradeData = $stmtGrade->fetch(PDO::FETCH_ASSOC);
            $gradeLevel = $gradeData['grade_level'] ?? null;
            $generalAverage = 'N/A';
            $subjects = [];
            if ($gradeLevel) {
                $totalSum = 0;
                $subjectCount = 0;
                if ($gradeLevel >= 7 && $gradeLevel < 10) {
                    // JHS Subjects and Grades
                    $subjectQuery = "
                        SELECT s.subject_code, s.subject_name, 
                               g.fstq_grade_tr, g.scndq_grade_tr, g.trdq_grade_tr, g.fthq_grade_tr 
                        FROM student_grade_jhs_tbl g
                        JOIN subjects_tbl s ON s.subject_id = g.subject_id
                        WHERE g.lrn = :lrn AND g.grade_term = :school_year  AND g.nested_id is null
                        AND s.archive = 0
                    ";
                    $stmtSubject = $pdo->prepare($subjectQuery);
                    $stmtSubject->execute(['lrn' => $lrn, 'school_year' => $schoolYear]);
                    $grades = $stmtSubject->fetchAll(PDO::FETCH_ASSOC);
                    if ($grades) {
                        foreach ($grades as $grade) {
                            $sum = floatval($grade['fstq_grade_tr'] ?? 0)
                                + floatval($grade['scndq_grade_tr'] ?? 0)
                                + floatval($grade['trdq_grade_tr'] ?? 0)
                                + floatval($grade['fthq_grade_tr'] ?? 0);
                            $avg = $sum / 4;
                            // Round the average to the nearest integer
                            $avg = round($avg);
                            $totalSum += $avg;
                            $subjectCount++;
                            $subjects[] = [
                                'subject_code' => $grade['subject_code'],
                                'subject_name' => $grade['subject_name'],
                                'grades' => [
                                    'fthq' => $grade['fthq_grade_tr'],
                                    'trdq' => $grade['trdq_grade_tr'],
                                    'scndq' => $grade['scndq_grade_tr'],
                                    'fstq' => $grade['fstq_grade_tr'],
                                ],
                                'average' => $avg // No need to use number_format here, since it's already rounded
                            ];
                        }
                    }
                } else {
                    // SHS Subjects and Grades
                    $subjectQuery = "
                        SELECT s.subject_code, s.subject_name, 
                               g.fsts_grade_tr, g.scnds_grade_tr
                        FROM student_grade_shs_tbl g
                        JOIN subjects_tbl s ON s.subject_id = g.subject_id
                        WHERE g.lrn = :lrn AND g.grade_term = :school_year AND s.archive = 0
                    ";
                    $stmtSubject = $pdo->prepare($subjectQuery);
                    $stmtSubject->execute(['lrn' => $lrn, 'school_year' => $schoolYear]);
                    $grades = $stmtSubject->fetchAll(PDO::FETCH_ASSOC);
                    if ($grades) {
                        foreach ($grades as $grade) {
                            $sum = floatval($grade['fsts_grade_tr'] ?? 0)
                                + floatval($grade['scnds_grade_tr'] ?? 0);
                            $avg = $sum / 2;
                            // Round the average to the nearest integer
                            $avg = round($avg);
                            $totalSum += $avg;
                            $subjectCount++;
                            $subjects[] = [
                                'subject_code' => $grade['subject_code'],
                                'subject_name' => $grade['subject_name'],
                                'grades' => [
                                    'scnds' => $grade['scnds_grade_tr'],
                                    'fsts' => $grade['fsts_grade_tr'],
                                ],
                                'average' => $avg // No need to use number_format here, since it's already rounded
                            ];
                        }
                    }
                }
                // Compute general average if subjects exist
                if ($subjectCount > 0) {
                    $generalAverage = number_format($totalSum / $subjectCount);
                } else {
                    $generalAverage = "00";
                }
            }
            // Build timeline item for this school year
            $timelineData[] = [
                'school_year' => $schoolYear,
                'health' => $healthData,
                'learning_materials' => $learningMaterials,
                'general_average' => $generalAverage,
                'grade_level' => $gradeLevel,
                'subjects' => $subjects
            ];
        }
    }
}
echo json_encode($timelineData);
exit;
?>