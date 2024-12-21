<?php
function route($uri, $method) {
    $uri = str_replace('/api', '', $uri);
    
    $uriParts = explode('/', trim($uri, '/'));

    if (count($uriParts) > 1) {
        $resource = $uriParts[0];
        $action = $uriParts[1];
        
        switch ($resource) {
            case 'students':
                require_once __DIR__ . '/../controllers/StudentInfoController.php';
                $controller = new StudentInfoController();
                
                if ($method === 'GET' && $action === 'getAllStudents') {
                    $controller->getAllStudents();
                }
                
                // Adding a student
                if ($method === 'POST' && $action === 'addStudent') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addStudent($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }

                // Updating a student
                if ($method === 'PUT' && $action === 'updateStudent') {
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

                if ($method === 'DELETE' && $action === 'deleteStudent') {
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
                break;

            case 'teachers':
                require_once __DIR__ . '/../controllers/TeacherController.php';
                $controller = new TeacherController();
                
                if ($method === 'GET' && $action === 'getAllTeachers') {
                    $controller->getAllTeachers();
                }
                if ($method === 'POST' && $action === 'AddTeacher') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addTeacher($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }
                if ($method === 'PUT' && $action === 'updateTeacher') {
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
                break;

            
            case 'classrooms':
                require_once __DIR__ . '/../controllers/ClassroomController.php';
                $controller = new ClassroomController();
                
                if ($method === 'GET' && $action === 'getAllClassrooms') {
                    $controller->getAllClassrooms();
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
                break;

                // Yearstudies
                case 'yearstudies':
                    require_once __DIR__ . '/../controllers/YearstudyController.php';
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
                    if ($method === 'PUT' && $action === 'updateYearStudy') {
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
                    
                    //subject case
                    case "sunjects" :
                        require_once __DIR__ . '/../controllers/SubjectController.php';
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
                        if($method === "PUT" && $action === "updateSubject"){
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
                        break;

                    case 'subsubjects':
                        require_once __DIR__ . '/../controllers/SubSubjectController.php';
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
                        if ($method === 'PUT' && $action === 'updateSubSubject') {
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
                        break;
    
            default:
                echo json_encode(['message' => 'Route not found']);
                break;
        }
        
    } else {
        echo json_encode(['message' => 'Route not found']);
    }
}