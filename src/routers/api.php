<?php
require_once dirname(__DIR__) . '/middleware/cors.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/controllers/UserController.php';
require_once dirname(__DIR__) . '/utils/response.php';
require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';
require_once dirname(__DIR__) . '/utils/Request.php';
require_once dirname(__DIR__) . '/utils/Response.php';

// Get the request URI and remove any query strings
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Initialize Request and Response objects
$req = new Request();
$res = new Response();

function route($uri, $method, $req, $res) {
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

                if ($method === 'GET' && $action === 'getStudentsByMonth') {
                    if (isset($uriParts[2])) {
                        $controller->getStudentsByMonth($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Month not provided']);
                    }
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

                if ($method === 'GET' && $action === 'getUnenrolledStudents'){
                    $controller->getUnenrolledStudents();
                    return;
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

                if ($method === 'GET' && $action === 'enrollmentByMonth' && isset($uriParts[2])) {
                    $controller->getEnrollmentCountsByMonth($uriParts[2]);
                    return;
                }

                if ($method === "POST" && $action === "promote" && isset($uriParts[2])) {
                    $controller->promoteStudent($uriParts[2]);
                }

                // Add route for promoting students by grade
                if ($method === "POST" && $action === "promoteByGrade") {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data && isset($data['current_grade_id']) && isset($data['new_grade_id'])) {
                        $controller->promoteStudentsByGrade($data['current_grade_id'], $data['new_grade_id']);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Missing required grade IDs'
                        ]);
                    }
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

                if ($method === 'GET' && $action === 'getUsersByClassId') {
                    if (isset($uriParts[2])) {
                        $controller->getUsersByClassId($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Class ID not provided']);
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

                // Assign Subject Grade Routes
                case 'assign_subject_grade':
                    require_once dirname(__DIR__) . '/controllers/AssignSubjectGradeController.php';
                    $controller = new AssignSubjectGradeController();
                    
                    // Get all assign subject grades (already assigned)
                    if($method === "GET" && $action === "getAllAssignSubjectGrades"){
                        $controller->getAllAssignSubjectGrades();
                    }

                    // Add subjects and/or sub-subjects to grade
                    if($method === "POST" && $action === "assignSubjectsToGrade"){
                        $data = json_decode(file_get_contents('php://input'), true);
                        if($data){
                            $controller->addSubjectsToGrade();
                        } else {
                            echo jsonResponse(400, ['message' => 'Invalid input data']);
                        }
                    }

                    // Get subjects and sub-subjects by grade ID
                    if($method === "GET" && $action === "getSubjectsByGradeId"){
                        if(isset($uriParts[2])){
                            $controller->getSubjectsByGradeId($uriParts[2]);
                        } else {
                            echo jsonResponse(400, ['message' => 'Grade ID not provided']);
                        }
                    }

                    // Delete subject or sub-subject from grade
                    if($method === "DELETE" && $action === "deleteSubjectFromGrade"){
                        if(isset($uriParts[2])){
                            $controller->deleteSubjectFromGrade($uriParts[2]);
                        } else {
                            echo jsonResponse(400, ['message' => 'Assign Subject Grade ID not provided']);
                        }
                    }
                    break;

                // Academic Routes
                case 'academics':
                    require_once dirname(__DIR__) . '/controllers/AcademicController.php';
                    $controller = new AcademicController();
                    
                    if ($method === 'GET' && $action === 'getAllAcademics') {
                        $controller->getAllAcademics();
                    }
                    if ($method === 'POST' && $action === 'addAcademic') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->AddAcademic($data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    }
                    if ($method === 'POST' && $action === 'updateAcademic') {
                        if (isset($uriParts[2])) {
                            $id = $uriParts[2];
                            $data = json_decode(file_get_contents('php://input'), true);
                            if ($data) {
                                $controller->updateAcademic($id, $data);
                            } else {
                                echo json_encode(['message' => 'Invalid input data']);
                            }
                        } else {
                            echo json_encode(['message' => 'Classroom ID not provided']);
                        }
                    }
                    if ($method === 'DELETE' && $action === 'deleteAcademic') {
                        if (isset($uriParts[2])) {
                            $controller->deleteAcademic($uriParts[2]);
                        } else {
                            echo json_encode(['message' => 'Academic ID not provided']);
                        }
                    
                    }
                    if ($method === 'GET' && $action === 'getAcademicById') {
                        if (isset($uriParts[2])) {
                            $controller->getAcademicById($uriParts[2]);
                        } else {
                            echo json_encode(['message' => 'Academic ID not provided']);
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
                            $controller->getAllSubSubjects();
                        }
                        if ($method === 'POST' && $action === 'addSubSubject') {
                            $data = json_decode(file_get_contents('php://input'), true);
                            if ($data) {
                                $controller->addSubSubject($data);
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
                        if ($method === "GET" && $action === "getSubSubjectsBySubject") {
                            if (isset($uriParts[2])) {
                                $controller->getSubSubjectsBySubjectCode($uriParts[2]);
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

                        // Monthly Score Routes
                        case 'monthly_score':
                            require_once dirname(__DIR__) . '/controllers/StudentMonthlyScoreController.php';
                            $controller = new StudentMonthlyScoreController();
                            
                            if ($method === "GET" && $action === "getAllMonthlyScores") {
                                $controller->getAllMonthlyScores();
                            }
                            
                            if ($method === "POST" && $action === "addMonthlyScore") {
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data) {
                                    $controller->addMonthlyScore($data);
                                }
                            }
                            
                            if ($method === "PUT" && $action === "updateMonthlyScore") {
                                if (isset($uriParts[2])) {
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if ($data) {
                                        $controller->updateMonthlyScore($uriParts[2], $data);
                                    }
                                }
                            }
                            
                            if ($method === "GET" && $action === "getStudentScoresByClassAndMonth") {
                                $controller->getStudentScoresByClassAndMonth();
                            }
                            
                            if ($method === "DELETE" && $action === "deleteMonthlyScore") {
                                if (isset($uriParts[2])) {
                                    $controller->deleteMonthlyScore($uriParts[2]);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getStudentMonthlyScores") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentMonthlyScores($uriParts[2]);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getClassMonthlyScores") {
                                if (isset($uriParts[2])) {
                                    $controller->getClassMonthlyScores($uriParts[2]);
                                }
                            }

                            if ($method === "GET" && $action === "getMonthlyScoreById") {
                                if (isset($uriParts[2])) {
                                    $controller->getMonthlyScoreById($uriParts[2]);
                                }
                            }

                            if ($method === "GET" && $action === "getAllScoresGrouped") {
                                $controller->getAllScoresGrouped();
                            }

                            if ($method === "GET" && $action === "getStudentScoresOrdered") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentScoresOrdered($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, [
                                        'status' => 'error',
                                        'message' => 'Student ID is required'
                                    ]);
                                }
                            }

                            if ($method === "GET" && $action === "getStudentScoresByClassSubjectMonthlyScore") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentScoresByClassSubjectMonthlyScore($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Classroom Subject Monthly Score ID not provided']);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getMonthlyScoresByFilters") {
                                $controller->getMonthlyScoresByFilters();
                            }
                            
                            if ($method === "GET" && $action === "getStudentMonthlyScoreSummary") {
                                $controller->getStudentMonthlyScoreSummary();
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

                        // User Routes
                        case 'users':
                            require_once dirname(__DIR__) . '/controllers/UserController.php';
                            require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';
                            $controller = new UserController();
                            
                            // Public routes
                            if ($method === 'POST' && $action === 'login') {
                                return $controller->login();
                            }

                            if ($method === 'GET' && $action === 'getUserById' && isset($uriParts[2])) {
                                $controller->getUserById($uriParts[2]);
                                return;
                            }

                            if ($method === 'GET' && $action === 'count') {
                                $controller->count();
                                return;
                            }

                            if($method === 'GET' && $action === 'searchUser' && isset($uriParts[2])){
                                $controller->searchUser($uriParts[2]);
                                return;
                            }

                            if($method === 'GET' && $action === 'getUser'){
                                $controller->getUser();
                                return;
                            }

                            if ($method === 'GET' && $action === 'getAllUsers') {
                                $controller->getAllUsers();
                                return;
                            }
                            if ($method === 'POST' && $action === 'updateStatus' && isset($uriParts[2])) {
                                $controller->updateStatus($uriParts[2]);
                                return;
                            }
                            
                            if ($method === 'POST' && $action === 'register') {
                                return $controller->register();
                            }
                            
                            // Protected routes - require authentication
                            if (!AuthMiddleware::verifyAuth()) {
                                return jsonResponse(401, ['error' => 'Unauthorized']);
                            }
                            
                            switch ($action) {
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

                        case 'classroom_subject_monthly_score':
                            require_once dirname(__DIR__) . '/controllers/ClassroomSubjectMonthlyScoreController.php';
                            $controller = new ClassroomSubjectMonthlyScoreController();
                            if($method === "GET" && $action === "getAllClassroomSubjectMonthlyScores"){
                                $controller->getAllClassroomSubjectMonthlyScores();
                            }
                            if($method === "GET" && $action === "getClassroomSubjectMonthlyScoreById"){
                                if(isset($uriParts[2])){
                                    $controller->getClassroomSubjectMonthlyScoreById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'ClassroomSubjectMonthlyScore ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getByClassSubjectMonthly"){
                                $controller->getByClassSubjectMonthly();
                            }
                            if($method === "GET" && $action === "getClassroomSubjectMonthlyScoresByClassId"){
                                if(isset($uriParts[2])){
                                    $controller->getClassroomSubjectMonthlyScoresByClassId($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Class ID not provided']);
                                }
                            }
                            if($method === "POST" && $action === "addClassroomSubjectMonthlyScore"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addClassroomSubjectMonthlyScore($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }

                            if($method === "POST" && $action === "updateClassroomSubjectMonthlyScore"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateClassroomSubjectMonthlyScore($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }else{
                                    echo json_encode(['message' => 'ClassroomSubjectMonthlyScore ID not provided']);
                                }
                            }

                            if($method === "DELETE" && $action === "deleteClassroomSubjectMonthlyScore"){
                                if(isset($uriParts[2])){
                                    $controller->deleteClassroomSubjectMonthlyScore($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'ClassroomSubjectMonthlyScore ID not provided']);
                                }
                            }

                            if($method === "GET" && $action === "getClassroomSubjectMonthlyScoresByClassId"){
                                if(isset($uriParts[2])){
                                    $controller->getClassroomSubjectMonthlyScoresByClassId($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Class ID not provided']);
                                }
                            }

                            if($method === "GET" && $action === "getClassroomSubjectMonthlyScoresByClassAndMonth"){
                                if(isset($uriParts[2]) && isset($uriParts[3])){
                                    $controller->getClassroomSubjectMonthlyScoresByClassAndMonth($uriParts[2], $uriParts[3]);
                                }else{
                                    echo json_encode(['message' => 'Class ID or Monthly ID not provided']);
                                }
                            }

                            if($method === "GET" && $action === "getClassroomSubjectMonthlyScoresbyMonthlyIdandClassId"){
                                if(isset($uriParts[2]) && isset($uriParts[3])){
                                    $controller->getClassroomSubjectMonthlyScoresbyMonthlyIdandClassId($uriParts[2], $uriParts[3]);
                                }else{
                                    echo json_encode(['message' => 'Monthly ID or Class ID not provided']);
                                }
                            }
                            break;
                        case 'semester_exam_subjects':
                            require_once dirname(__DIR__) . '/controllers/SemesterExamSubjectsController.php';
                            $controller = new SemesterExamSubjectsController();
                            if($method === "GET" && $action === "getAllSemesterExamSubjects"){
                                $controller->getAllSemesterExamSubjects();
                            }

                            if($method === "GET" && $action === "getByClassSubjectSemester"){
                                $controller->getByClassSubjectSemester();
                            }
                            
                            if($method === "GET" && $action === "getSemesterExamSubjectByClassId"){
                                if(isset($uriParts[2])){
                                    $controller->getSemesterExamSubjectByClassId($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Class ID not provided']);
                                }
                            }

                            if($method === "GET" && $action === "getAvailableMonthlyScores"){
                                if(isset($uriParts[2])){
                                    $controller->getAvailableMonthlyScores($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Class ID not provided']);
                                }
                            }
                            
                            if($method === "POST" && $action === "addSemesterExamSubject"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addSemesterExamSubject($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateSemesterExamSubject"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateSemesterExamSubject($id, $data);
                                    } else {
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                } else {
                                    // No ID provided, check for batch update
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data && isset($data['update_all']) && $data['update_all'] === true) {
                                        // Pass an empty ID to indicate batch update
                                        $controller->updateSemesterExamSubject(null);
                                    } else {
                                        echo json_encode(['message' => 'SemesterExamSubject ID not provided']);
                                    }
                                }
                            }
                                                        
                            if($method === "DELETE" && $action === "deleteSemesterExamSubject"){
                                if(isset($uriParts[2])){
                                    $controller->deleteSemesterExamSubject($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'SemesterExamSubject ID not provided']);
                                }
                            }
                            break;

                        case 'student_semester_score':
                            require_once dirname(__DIR__) . '/controllers/StudentSemesterScoreController.php';
                            $controller = new StudentSemesterScoreController();
                            if($method === "GET" && $action === "getStudentSemesterScores"){
                                $controller->getStudentSemesterScores($req, $res);
                            }
                            if($method === "POST" && $action === "calculateFinalSemesterAverage"){
                                $controller->calculateFinalSemesterAverage($req, $res);
                            }

                            if($method === "GET" && $action === "getStudentScoresByClassAndSemester"){
                                $controller->getStudentScoresByClassAndSemester();
                            }

                            if($method === "GET" && $action === "getAllStudentSemesterScores"){
                                $controller->getAllStudentSemesterScores();
                            }
                            if($method === "POST" && $action === "addStudentSemesterScore"){
                                $controller->addStudentSemesterScore($req, $res);
                            }
                            if($method === "POST" && $action === "updateStudentSemesterScore"){
                                // Expecting ID in the URL now: /updateStudentSemesterScore/{id}
                                if (isset($uriParts[2])) {
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    $controller->updateStudentSemesterScore($id, $data);
                                } else {
                                    echo jsonResponse(400, ['message' => 'StudentSemesterScore ID not provided in URL']);
                                }
                            }
                            if($method === "DELETE" && $action === "deleteStudentSemesterScore"){
                                $controller->deleteStudentSemesterScore($req, $res);
                            }
                            
                            break;
                        case 'study':
                            require_once dirname(__DIR__) . '/controllers/StudyController.php';
                            $controller = new StudyController();
                            
                            if ($method === "GET" && $action === "getAllStudies") {
                                $controller->getAllStudies();
                            }

                            if ($method === "GET" && $action === "getStudentsByGradeId") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentsByGradeId($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Grade ID not provided']);
                                }
                            }

                            if ($method === "GET" && $action === "getStudentsByGradeIdWithHighScores") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentRankingsByGradeId($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Grade ID not provided']);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getStudiesByStudentId") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudiesByStudentId($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Student ID not provided']);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getStudiesByClassId") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudiesByClassId($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Class ID not provided']);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getStudiesByYearId") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudiesByYearId($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Year ID not provided']);
                                }
                            }
                            
                            if ($method === "POST" && $action === "addStudy") {
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data) {
                                    $controller->addStudy($data);
                                } else {
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }

                            if ($method === "POST" && $action === "addMultipleStudies") {
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data) {
                                    $controller->addMultipleStudies($data);
                                } else {
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            
                            if ($method === "PUT" && $action === "updateStudy") {
                                if (isset($uriParts[2])) {
                                    $controller->updateStudy($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Study ID not provided']);
                                }
                            }
                            
                            if ($method === "DELETE" && $action === "deleteStudy") {
                                if (isset($uriParts[2])) {
                                    $controller->deleteStudy($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Study ID not provided']);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getStudyById") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudyById($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Study ID not provided']);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getCurrentClassForStudent") {
                                if (isset($uriParts[2])) {
                                    $controller->getCurrentClassForStudent($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Student ID not provided']);
                                }
                            }

                            if ($method === "GET" && $action === "getCurrentEnrollment") {
                                if (isset($uriParts[2])) {
                                    $controller->getCurrentEnrollment($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, ['message' => 'Class ID not provided']);
                                }
                            }
                            
                            if ($method === "POST" && $action === "promoteByClass") {
                                $controller->promoteByClass();
                            }
                            
                            if ($method === "POST" && $action === "promoteStudent") {
                                $controller->promoteStudent();
                            }
                            break;
                        case 'studies':
                            require_once dirname(__DIR__) . '/controllers/StudyController.php';
                            $controller = new StudyController();

                            if ($method === 'GET' && $action === 'getAllStudies') {
                                $controller->getAllStudies();
                            }

                            if ($method === 'GET' && $action === 'getStudiesByStudentId') {
                                if (isset($uriParts[2])) {
                                    $controller->getStudiesByStudentId($uriParts[2]);
                                } else {
                                    echo json_encode(['message' => 'Student ID not provided']);
                                }
                            }

                            if ($method === 'GET' && $action === 'getStudiesByClassId') {
                                if (isset($uriParts[2])) {
                                    $controller->getStudiesByClassId($uriParts[2]);
                                } else {
                                    echo json_encode(['message' => 'Class ID not provided']);
                                }
                            }
                            break;
                        case 'reports':
                            require_once dirname(__DIR__) . '/controllers/ReportController.php';
                            $controller = new ReportController();

                            if ($method === 'GET' && $action === 'getStudentMonthlyScoreReport') {
                                $controller->getStudentMonthlyScoreReport();
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
route($uri, $_SERVER['REQUEST_METHOD'], $req, $res);