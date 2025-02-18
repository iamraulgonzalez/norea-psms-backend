<?php
require_once dirname(__DIR__) . '/middleware/cors.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/controllers/UserController.php';
require_once dirname(__DIR__) . '/utils/response.php';
require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';

// Get the request URI and remove any query strings
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function route($uri, $method) {
    $uri = str_replace('/api', '', $uri);
    $uriParts = explode('/', trim($uri, '/'));

    // Public routes
    if ($method === 'POST' && $uri === '/users/login') {
        try {
            $controller = new UserController();
            return $controller->login();
        } catch (Exception $e) {
            error_log("Login route error: " . $e->getMessage());
            return jsonResponse(500, ['error' => $e->getMessage()]);
        }
    }

    // Verify token route
    if ($method === 'GET' && $uri === '/users/verify-token') {
        AuthMiddleware::authenticate();
        $controller = new UserController();
        return $controller->verifyToken();
    }

    // Protected routes - require authentication
    if (!AuthMiddleware::verifyAuth()) {
        return jsonResponse(401, ['error' => 'Unauthorized']);
    }

    if (count($uriParts) > 1) {
        $resource = $uriParts[0];
        $action = $uriParts[1];
        
        switch ($resource) {
            case 'students':
                require_once dirname(__DIR__) . '/controllers/StudentInfoController.php';
                $controller = new StudentInfoController();
                
                if ($method === 'GET' && $action === 'getAllStudents') {
                    $controller->getAllStudents();
                    return;
                }
                if ($method === 'GET' && $action === 'getStudentsByClassId') {
                    if (isset($uriParts[2])) {
                        $controller->getStudentsByClassId($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Class ID not provided']);
                    }
                }
                if ($method === 'POST' && $action === 'addStudent') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addStudent($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }

                if ($method === 'POST' && $action === 'updateStudent') {
                    if (isset($uriParts[2])) {
                        $id = $uriParts[2];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->updateStudent($id, $data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    } else {
                        echo json_encode(['message' => 'Student ID not provided']);
                    }
                }

                if ($method === 'POST' && $action === 'deleteStudent') {
                    if (isset($uriParts[2])) {
                        $controller->deleteStudent($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Student ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'getStudentById') {
                    if (isset($uriParts[2])) {
                        $controller->getStudentById($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Student ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'count') {
                    $controller->getStudentCount();
                    return;
                }
                break;

            case 'teachers':
                require_once dirname(__DIR__) . '/controllers/TeacherController.php';
                $controller = new TeacherController();
                
                if ($method === 'GET' && $action === 'getAllTeachers') {
                    $controller->getAllTeachers();
                }
                if ($method === 'GET' && $action === 'getTeachersByClassId') {
                    if (isset($uriParts[2])) {
                        $controller->getTeachersByClassId($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Class ID not provided']);
                    }
                }
                if ($method === 'POST' && $action === 'AddTeacher') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addTeacher($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }
                if ($method === 'POST' && $action === 'updateTeacher') {
                    if (isset($uriParts[2])) {
                        $id = $uriParts[2];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->updateTeacher($id, $data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    } else {
                        echo json_encode(['message' => 'Teacher ID not provided']);
                    }
                }
                if ($method === 'DELETE' && $action === 'deleteTeacher') {
                    if (isset($uriParts[2])) {
                        $controller->deleteTeacher($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Teacher ID not provided']);
                    }
                }
                if ($method === 'GET' && $action === 'getTeacherById') {
                    if (isset($uriParts[2])) {
                        $controller->getTeacherById($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Teacher ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'count') {
                    $controller->getTeacherCount();
                    return;
                }
                break;
            
            case 'classrooms':
                require_once dirname(__DIR__) . '/controllers/ClassroomController.php';
                $controller = new ClassroomController();
                
                if ($method === 'GET' && $action === 'getAllClassrooms') {
                    $controller->getAllClassrooms();
                }
                if ($method === 'GET' && $action === 'getClassesByGrade') {
                    if (isset($uriParts[2])) {
                        $controller->getClassesByGrade($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Grade ID not provided']);
                    }
                }
                if ($method === 'POST' && $action === 'addClassroom') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addClassroom($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }
                if ($method === 'PUT' && $action === 'updateClassroom') {
                    if (isset($uriParts[2])) {
                        $id = $uriParts[2];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->updateClassroom($id, $data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    } else {
                        echo json_encode(['message' => 'Classroom ID not provided']);
                    }
                }
                if ($method === 'DELETE' && $action === 'deleteClassroom') {
                    if (isset($uriParts[2])) {
                        $controller->deleteClassroom($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Classroom ID not provided']);
                    }
                
                }
                if ($method === 'GET' && $action === 'getClassroomById') {
                    if (isset($uriParts[2])) {
                        $controller->getClassroomById($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Classroom ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'count') {
                    $controller->getClassroomCount();
                }
                break;
                case 'classroom_subjects':
                    require_once dirname(__DIR__) . '/controllers/ClassroomSubjectController.php';
                    $controller = new ClassroomSubjectController();
                    if($method === "POST" && $action === "assignSubjects"){
                        $data = json_decode(file_get_contents('php://input'), true);
                        if($data){
                            $controller->addSubjectsToClass($data);
                        }
                    }

                    if($method === "GET" && $action === "getClassSubjects"){
                        if(isset($uriParts[2])){
                            $controller->getClassSubjects($uriParts[2]);
                        }else{
                            echo json_encode(['message' => 'Class ID not provided']);
                        }
                    }
                    if($method === "DELETE" && $action === "deleteSubjectFromClass"){
                        if(isset($uriParts[2])){
                            $controller->deleteSubjectFromClass($uriParts[2], $uriParts[3]);

                        }else{
                            echo json_encode(['message' => 'Class ID and Subject Code not provided']);
                        }
                    }
                    break;
                case 'yearstudies':

                    require_once dirname(__DIR__) . '/controllers/YearstudyController.php';

                    $controller = new YearstudyController();
                    

                    if ($method === 'GET' && $action === 'getAllYearStudies') {
                        $controller->getAllYearStudies();
                    }
                    if ($method === 'POST' && $action === 'addYearStudy') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->AddYearStudy($data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    }
                    if ($method === 'POST' && $action === 'updateYearStudy') {
                        if (isset($uriParts[2])) {
                            $id = $uriParts[2];
                            $data = json_decode(file_get_contents('php://input'), true);
                            if ($data) {
                                $controller->updateYearStudy($id, $data);
                            } else {
                                echo json_encode(['message' => 'Invalid input data']);
                            }
                        } else {
                            echo json_encode(['message' => 'Classroom ID not provided']);
                        }
                    }
                    if ($method === 'DELETE' && $action === 'deleteYearStudy') {
                        if (isset($uriParts[2])) {
                            $controller->deleteYearStudy($uriParts[2]);
                        } else {
                            echo json_encode(['message' => 'YearStudy ID not provided']);
                        }
                    
                    }
                    if ($method === 'GET' && $action === 'getYearStudyById') {
                        if (isset($uriParts[2])) {
                            $controller->getYearStudyById($uriParts[2]);
                        } else {
                            echo json_encode(['message' => 'YearStudy ID not provided']);
                        }
                    }
                    break;
                    
                    // Subject Routes
                    case "subjects" :
                        require_once dirname(__DIR__) . '/controllers/SubjectController.php';
                        $controller = new SubjectController();
                        if($method === "GET" && $action === "getAllSubjects"){
                            $controller->getAllSubject();
                        }
                        if($method === "POST" && $action === "addSubject"){
                            $data = json_decode(file_get_contents('php://input'), true);
                            if($data){
                                $controller->AddSubject($data);
                            }else{
                                echo json_encode(['message' => 'Invalid input data']);
                            }
                        }
                        if($method === "POST" && $action === "updateSubject"){
                            if(isset($uriParts[2])){
                                $id = $uriParts[2];
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->updateSubject($id, $data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }else{
                                echo json_encode(['message' => 'Subject ID not provided']);
                            }
                        }
                        if($method === "DELETE" && $action === "deleteSubject"){
                            if(isset($uriParts[2])){
                                $controller->deleteSubject($uriParts[2]);
                            }else{
                                echo json_encode(['message' => 'Subject ID not provided']);
                            }
                        
                        }
                        if($method === "GET" && $action === "getSubjectById"){
                            if(isset($uriParts[2])){
                                $controller->getSubjectById($uriParts[2]);
                            }else{
                                echo json_encode(['message' => 'Subject ID not provided']);
                            }
                        }

                        if ($method === 'GET' && $action === 'count') {
                            $controller->getSubjectCount();
                            return;
                        }
                    break;

                     // SubSubject Routes
                    case 'subsubjects':
                        require_once dirname(__DIR__) . '/controllers/SubSubjectController.php';
                        $controller = new SubSubjectController();
                        if ($method === 'GET' && $action === 'getAllSubSubjects') {
                            $controller->getAllSubSubject();
                        }
                        if ($method === 'POST' && $action === 'addSubSubject') {
                            $data = json_decode(file_get_contents('php://input'), true);
                            if ($data) {
                                $controller->AddSubSubject($data);
                            } else {
                                echo json_encode(['message' => 'Invalid input data']);
                            }
                        }
                        if ($method === 'POST' && $action === 'updateSubSubject') {
                            if (isset($uriParts[2])) {
                                $id = $uriParts[2];
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data) {
                                    $controller->updateSubSubject($id, $data);
                                } else {
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            } else {
                                echo json_encode(['message' => 'SubSubject ID not provided']);
                            }
                        }
                        if ($method === 'DELETE' && $action === 'deleteSubSubject') {
                            if (isset($uriParts[2])) {
                                $controller->deleteSubSubject($uriParts[2]);
                            } else {
                                echo json_encode(['message' => 'SubSubject ID not provided']);
                            }
                        }
                        if ($method === 'GET' && $action === 'getSubSubjectById') {
                            if (isset($uriParts[2])) {
                                $controller->getSubSubjectById($uriParts[2]);
                            } else {
                                echo json_encode(['message' => 'SubSubject ID not provided']);
                            }
                        }
                        if ($method === 'GET' && $action === 'getBySubjectCode') {
                            if (isset($uriParts[2])) {
                                $controller->getSubSubjectsBySubjectCode($uriParts[2]);
                            } else {
                                echo jsonResponse(400, ['message' => 'Subject code not provided']);
                            }
                        }
                        break;

                        // Assign Monthly Subject Grade Routes
                        case 'assign_monthly_subject_grade':
                            require_once dirname(__DIR__) . '/controllers/AssignMonthlySubjectGradeController.php';
                            $controller = new AssignMonthlySubjectGradeController();
                            if($method === "GET" && $action === "getAllAssignMonthlySubjectGrades"){
                                $controller->getAllAssignMonthlySubjectGrades();
                            }
                            if($method === "POST" && $action === "addAssignMonthlySubjectGrade"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addAssignMonthlySubjectGrade($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "PUT" && $action === "updateAssignMonthlySubjectGrade"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateAssignMonthlySubjectGrade($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteAssignMonthlySubjectGrade"){
                                if(isset($uriParts[2])){
                                    $controller->deleteAssignMonthlySubjectGrade($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'AssignMonthlySubjectGrade ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getAssignMonthlySubjectGradeById"){
                                if(isset($uriParts[2])){
                                    $controller->getAssignMonthlySubjectGradeById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'AssignMonthlySubjectGrade ID not provided']);
                                }
                            }
                        break;

                        // Semester Routes
                        case 'semesters':
                            require_once dirname(__DIR__) . '/controllers/SemesterController.php';
                            $controller = new SemesterController();
                            if($method === "GET" && $action === "getAllSemesters"){
                                $controller->getAllSemesters();
                            }
                            if($method === "POST" && $action === "addSemester"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addSemester($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateSemester"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateSemester($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteSemester"){
                                if(isset($uriParts[2])){
                                    $controller->deleteSemester($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Semester ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getSemesterById"){
                                if(isset($uriParts[2])){
                                    $controller->getSemesterById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Semester ID not provided']);
                                }
                            }
                        break;

                        // Semester Score Routes
                        case 'semester_score':
                            require_once dirname(__DIR__) . '/controllers/SemesterScoreController.php';
                            $controller = new SemesterScoreController();
                            if($method === "GET" && $action === "getAllSemesterScore"){
                                $controller->getAllSemesterScore();
                            }
                            if($method === "POST" && $action === "addSemesterScore"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addSemesterScore($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateSemesterScore"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateSemesterScore($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteSemesterScore"){
                                if(isset($uriParts[2])){
                                    $controller->deleteSemesterScore($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'SemesterScore ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getSemesterScoreById"){
                                if(isset($uriParts[2])){
                                    $controller->getSemesterScoreById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'SemesterScore ID not provided']);
                                }
                            }
                        break;

                        case 'monthly_score':
                            require_once dirname(__DIR__) . '/controllers/MonthlyScoreController.php';
                            $controller = new MonthlyScoreController();
                            if ($method === "GET" && $action === "getAllMonthlyScores") {
                                $controller->getAllMonthlyScores();
                            }
                            
                            if ($method === "GET" && $action === "getStudentMonthlyScores") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentMonthlyScores($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, [
                                        'status' => 'error',
                                        'message' => 'Student ID not provided'
                                    ]);
                                }
                            }
                            
                            if ($method === "POST" && $action === "addMonthlyScore") {
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data) {
                                    $controller->addMonthlyScore($data);
                                } else {
                                    echo jsonResponse(400, [
                                        'status' => 'error',
                                        'message' => 'Invalid input data'
                                    ]);
                                }
                            }
                            
                            if ($method === "PUT" && $action === "updateMonthlyScore") {
                                if (isset($uriParts[2])) {
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if ($data) {
                                        $controller->updateMonthlyScore($uriParts[2], $data);
                                    } else {
                                        echo jsonResponse(400, [
                                            'status' => 'error',
                                            'message' => 'Invalid input data'
                                        ]);
                                    }
                                } else {
                                    echo jsonResponse(400, [
                                        'status' => 'error',
                                        'message' => 'Monthly Score ID not provided'
                                    ]);
                                }
                            }
                            
                            if ($method === "DELETE" && $action === "deleteMonthlyScore") {
                                if (isset($uriParts[2])) {
                                    $controller->deleteMonthlyScore($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, [
                                        'status' => 'error',
                                        'message' => 'Monthly Score ID not provided'
                                    ]);
                                }
                            }
                        break;

                        // Monthly Routes
                        case 'monthly':
                            require_once dirname(__DIR__) . '/controllers/MonthlyController.php';
                            $controller = new MonthlyController();
                            if($method === "GET" && $action === "getAllMonthly"){
                                $controller->getAllMonthlies();
                            }
                            if($method === "POST" && $action === "addMonthly"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addMonthly($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateMonthly"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateMonthly($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteMonthly"){
                                if(isset($uriParts[2])){
                                    $controller->deleteMonthly($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Monthly ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getMonthlyById"){
                                if(isset($uriParts[2])){
                                    $controller->getMonthlyById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Monthly ID not provided']);
                                }
                            }
                        break;

                        // Month Attendance Routes
                        case 'month_attendance':
                            require_once dirname(__DIR__) . '/controllers/MonthAttendanceController.php';
                            $controller = new MonthAttendanceController();
                            if($method === "GET" && $action === "getAllMonthAttendance"){
                                $controller->getAllMonthAttendances();
                            }
                            if($method === "POST" && $action === "addMonthAttendance"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addMonthAttendance($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "PUT" && $action === "updateMonthAttendance"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateMonthAttendance($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteMonthAttendance"){
                                if(isset($uriParts[2])){
                                    $controller->deleteMonthAttendance($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'MonthAttendance ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getMonthAttendanceById"){
                                if(isset($uriParts[2])){
                                    $controller->getMonthAttendanceById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'MonthAttendance ID not provided']);
                                }
                            }
                        break;

                        // Grade Routes
                        case 'grade':
                            require_once dirname(__DIR__) . '/controllers/GradeController.php';
                            $controller = new GradeController();
                            if($method === "GET" && $action === "getAllGrades"){
                                $controller->getAllGrades();
                            }
                            if($method === "POST" && $action === "addGrade"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addGrade($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateGrade"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateGrade($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteGrade"){
                                if(isset($uriParts[2])){
                                    $controller->deleteGrade($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Grade ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getGradeById"){
                                if(isset($uriParts[2])){
                                    $controller->getGradeById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Grade ID not provided']);
                                }
                            }
                        break;

                        // Assign Semester Subject Grade Routes
                        case 'assignsemestersubjectgrade':
                            require_once dirname(__DIR__) . '/controllers/AssignSemesterSubjectGradeController.php';
                            $controller = new AssignSemesterSubjectGradeController();
                            if($method === "GET" && $action === "getAllAssignSemesterSubjectGrades"){
                                $controller->getAllAssignSemesterSubjectGrades();
                            }
                            if($method === "GET" && $action === "getAllAssignSemesterSubjectGradesByGradeId"){
                                if(isset($uriParts[2])){
                                    $controller->getAllAssignSemesterSubjectGradesByGradeId($uriParts[2]);

                                }else{
                                    echo json_encode(['message' => 'Grade ID not provided']);
                                }
                            }
                            if($method === "POST" && $action === "addAssignSemesterSubjectGrade"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addAssignSemesterSubjectGrade($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateAssignSemesterSubjectGrade"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateAssignSemesterSubjectGrade($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteAssignSemesterSubjectGrade"){
                                if(isset($uriParts[2])){
                                    $controller->deleteAssignSemesterSubjectGrade($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'AssignSemesterSubjectGrade ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getAssignSemesterSubjectGradeById"){
                                if(isset($uriParts[2])){
                                    $controller->getAssignSemesterSubjectGradeById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'AssignSemesterSubjectGrade ID not provided']);
                                }
                            }
                        break;

                        // User Routes
                        case 'users':
                            require_once dirname(__DIR__) . '/controllers/UserController.php';
                            require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';
                            $controller = new UserController();
                            
                            // Public routes
                            if ($method === 'POST' && $action === 'login') {
                                return $controller->login();
                            }
                            
                            if ($method === 'POST' && $action === 'register') {
                                return $controller->register();
                            }
                            
                            // Protected routes - require authentication
                            if (!AuthMiddleware::verifyAuth()) {
                                return jsonResponse(401, ['error' => 'Unauthorized']);
                            }
                            
                            switch ($action) {
                                case 'getAllUsers':
                                    return $controller->getAllUsers();
                                case 'update':
                                    if (isset($uriParts[2])) {
                                        return $controller->update($uriParts[2]);
                                    }
                                    break;
                                case 'logout':
                                    return $controller->logout();
                                case 'current':
                                    return $controller->getCurrentUser();
                            }
                            break;
                        case 'school_session':
                            require_once dirname(__DIR__) . '/controllers/SchoolSessionController.php';
                            $controller = new SchoolSessionController();
                            if($method === "GET" && $action === "getAllSchoolSessions"){
                                $controller->getAllSchoolSessions();
                            }


                            if($method === "GET" && $action === "getSchoolSessionById"){
                                if(isset($uriParts[2])){
                                    $controller->getSchoolSessionById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'SchoolSession ID not provided']);
                                }
                            }
                        break;
                            
            default:
                echo json_encode(['message' => 'Route not found']);


                break;
        }
        
    } else {
        echo json_encode(['message' => 'Route not found']);
    }
}

// Execute routing
route($uri, $_SERVER['REQUEST_METHOD']);