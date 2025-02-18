<?php
return [
    'students' => [
        'controller' => 'StudentInfoController',
        'actions' => [
            'GET' => [
                'getAllStudents' => 'getAllStudents',
                'getStudentById' => 'getStudentById'
            ],
            'POST' => [
                'addStudent' => 'addStudent'
            ],
            'PUT' => [
                'updateStudent' => 'updateStudent'
            ],
            'DELETE' => [
                'deleteStudent' => 'deleteStudent'
            ]
        ]
    ],
    'teachers' => [
        'controller' => 'TeacherController',
        'actions' => [
            'GET' => [
                'getAllTeachers' => 'getAllTeachers',
                'getTeacherById' => 'getTeacherById'
            ],
            'POST' => [
                'AddTeacher' => 'addTeacher'
            ],
            'PUT' => [
                'updateTeacher' => 'updateTeacher'
            ],
            'DELETE' => [
                'deleteTeacher' => 'deleteTeacher'
            ]
        ]
    ],
    'classrooms' => [
        'controller' => 'ClassroomController',
        'actions' => [
            'GET' => [
                'getAllClassrooms' => 'getAllClassrooms',
                'getClassroomById' => 'getClassroomById'
            ],
            'POST' => [
                'addClassroom' => 'addClassroom'
            ],
            'PUT' => [
                'updateClassroom' => 'updateClassroom'
            ],
            'DELETE' => [
                'deleteClassroom' => 'deleteClassroom'
            ]
        ]
    ],
    'yearstudies' => [
        'controller' => 'YearstudyController',
        'actions' => [
            'GET' => [
                'getAllYearStudies' => 'getAllYearStudies',
                'getYearStudyById' => 'getYearStudyById'
            ],
            'POST' => [
                'addYearStudy' => 'AddYearStudy'
            ],
            'PUT' => [
                'updateYearStudy' => 'updateYearStudy'
            ],
            'DELETE' => [
                'deleteYearStudy' => 'deleteYearStudy'
            ]
        ]
    ],
    'subjects' => [
        'controller' => 'SubjectController',
        'actions' => [
            'GET' => [
                'getAllSubjects' => 'getAllSubject',
                'getSubjectById' => 'getSubjectById'
            ],
            'POST' => [
                'addSubject' => 'AddSubject'
            ],
            'PUT' => [
                'updateSubject' => 'updateSubject'
            ],
            'DELETE' => [
                'deleteSubject' => 'deleteSubject'
            ]
        ]
    ],
    'subsubjects' => [
        'controller' => 'SubSubjectController',
        'actions' => [
            'GET' => [
                'getAllSubSubjects' => 'getAllSubSubject',
                'getSubSubjectById' => 'getSubSubjectById'
            ],
            'POST' => [
                'addSubSubject' => 'AddSubSubject'
            ],
            'PUT' => [
                'updateSubSubject' => 'updateSubSubject'
            ],
            'DELETE' => [
                'deleteSubSubject' => 'deleteSubSubject'
            ]
        ]
    ],
    'assign_monthly' => [
        'controller' => 'AssignMonthlySubjectGradeController',
        'actions' => [
            'GET' => [
                'getAllAssignMonthlySubjectGrades' => 'getAllAssignMonthlySubjectGrades',
                'getAssignMonthlySubjectGradeById' => 'getAssignMonthlySubjectGradeById'
            ],
            'POST' => [
                'addAssignMonthlySubjectGrade' => 'addAssignMonthlySubjectGrade'
            ],
            'PUT' => [
                'updateAssignMonthlySubjectGrade' => 'updateAssignMonthlySubjectGrade'
            ],
            'DELETE' => [
                'deleteAssignMonthlySubjectGrade' => 'deleteAssignMonthlySubjectGrade'
            ]
        ]
    ],
    'semesters' => [
        'controller' => 'SemesterController',
        'actions' => [
            'GET' => [
                'getAllSemesters' => 'getAllSemesters',
                'getSemesterById' => 'getSemesterById'
            ],
            'POST' => [
                'addSemester' => 'addSemester'
            ],
            'PUT' => [
                'updateSemester' => 'updateSemester'
            ],
            'DELETE' => [
                'deleteSemester' => 'deleteSemester'
            ]
        ]
    ],
    'semester_score' => [
        'controller' => 'SemesterScoreController',
        'actions' => [
            'GET' => [
                'getAllSemesterScore' => 'getAllSemesterScore',
                'getSemesterScoreById' => 'getSemesterScoreById'
            ],
            'POST' => [
                'addSemesterScore' => 'addSemesterScore'
            ],
            'PUT' => [
                'updateSemesterScore' => 'updateSemesterScore'
            ],
            'DELETE' => [
                'deleteSemesterScore' => 'deleteSemesterScore'
            ]
        ]
    ],
    'monthly_score' => [
        'controller' => 'MonthlyScoreController',
        'actions' => [
            'GET' => [
                'getAllMonthlyScore' => 'getAllMonthlyScores',
                'getMonthlyScoreById' => 'getMonthlyScoreById'
            ],
            'POST' => [
                'addMonthlyScore' => 'addMonthlyScore'
            ],
            'PUT' => [
                'updateMonthlyScore' => 'updateMonthlyScore'
            ],
            'DELETE' => [
                'deleteMonthlyScore' => 'deleteMonthlyScore'
            ]
        ]
    ],
    'monthly' => [
        'controller' => 'MonthlyController',
        'actions' => [
            'GET' => [
                'getAllMonthly' => 'getAllMonthlies',
                'getMonthlyById' => 'getMonthlyById'
            ],
            'POST' => [
                'addMonthly' => 'addMonthly'
            ],
            'PUT' => [
                'updateMonthly' => 'updateMonthly'
            ],
            'DELETE' => [
                'deleteMonthly' => 'deleteMonthly'
            ]
        ]
    ],
    'month_attendance' => [
        'controller' => 'MonthAttendanceController',
        'actions' => [
            'GET' => [
                'getAllMonthAttendance' => 'getAllMonthAttendances',
                'getMonthAttendanceById' => 'getMonthAttendanceById'
            ],
            'POST' => [
                'addMonthAttendance' => 'addMonthAttendance'
            ],
            'PUT' => [
                'updateMonthAttendance' => 'updateMonthAttendance'
            ],
            'DELETE' => [
                'deleteMonthAttendance' => 'deleteMonthAttendance'
            ]
        ]
    ],
    'grade' => [
        'controller' => 'GradeController',
        'actions' => [
            'GET' => [
                'getAllGrades' => 'getAllGrades',
                'getGradeById' => 'getGradeById'
            ],
            'POST' => [
                'addGrade' => 'addGrade'
            ],
            'PUT' => [
                'updateGrade' => 'updateGrade'
            ],
            'DELETE' => [
                'deleteGrade' => 'deleteGrade'
            ]
        ]
            ]
]; 