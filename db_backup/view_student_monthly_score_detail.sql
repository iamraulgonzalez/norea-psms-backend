-- Create a view for detailed student monthly scores
CREATE OR REPLACE VIEW view_student_monthly_score_detail AS
SELECT
    s.student_id,
    s.student_name,
    c.class_id,
    c.class_name,
    m.monthly_id,
    m.month_name,
    sub.subject_id,
    sub.subject_name,
    sub.subject_code,
    sms.score
FROM
    tbl_student_info s
    INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
    INNER JOIN tbl_classroom c ON st.class_id = c.class_id
    INNER JOIN classroom_subject_monthly_score csms ON c.class_id = csms.class_id
    INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
    INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
    INNER JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
    LEFT JOIN tbl_student_monthly_score sms ON 
        s.student_id = sms.student_id 
        AND sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
        AND sms.isDeleted = 0
WHERE
    s.isDeleted = 0
    AND csms.isDeleted = 0;

-- Create a new view that pivots subject scores with self-calculated aggregates
CREATE OR REPLACE VIEW `view_student_monthly_score_pivot` AS
WITH student_subjects AS (
    SELECT 
        student_id,
        student_name,
        class_id,
        class_name,
        monthly_id,
        month_name,
        subject_name,
        score
    FROM 
        view_student_monthly_score_detail
),
student_aggregates AS (
    SELECT
        student_id,
        class_id,
        monthly_id,
        COUNT(DISTINCT subject_name) AS subjects_count,
        SUM(score) AS total_score,
        AVG(score) AS average_score
    FROM
        student_subjects
    GROUP BY
        student_id, class_id, monthly_id
),
rankings AS (
    SELECT
        a.*,
        RANK() OVER (PARTITION BY a.class_id, a.monthly_id ORDER BY a.average_score DESC) AS rank_in_class,
        COUNT(*) OVER (PARTITION BY a.class_id, a.monthly_id) AS class_size
    FROM
        student_aggregates a
)
SELECT
    s.student_id,
    s.student_name,
    s.class_id,
    s.class_name,
    s.monthly_id,
    s.month_name,
    r.subjects_count,
    r.total_score,
    r.average_score,
    r.rank_in_class,
    r.class_size,
    MAX(CASE WHEN s.subject_name = 'ភាសាខ្មែរ' THEN s.score ELSE NULL END) AS 'ភាសាខ្មែរ',
    MAX(CASE WHEN s.subject_name = 'គណិតវិទ្យា' THEN s.score ELSE NULL END) AS 'គណិតវិទ្យា',
    MAX(CASE WHEN s.subject_name = 'វិទ្យាសាស្រ្ត' THEN s.score ELSE NULL END) AS 'វិទ្យាសាស្រ្ត',
    MAX(CASE WHEN s.subject_name = 'សង្គមសិក្សា' THEN s.score ELSE NULL END) AS 'សង្គមសិក្សា',
    MAX(CASE WHEN s.subject_name = 'ភាសាអង់គ្លេស' THEN s.score ELSE NULL END) AS 'ភាសាអង់គ្លេស',
    -- Add any other known subjects here
    MAX(CASE WHEN s.subject_name = 'គំនូរ' THEN s.score ELSE NULL END) AS 'គំនូរ',
    MAX(CASE WHEN s.subject_name = 'ចំរៀង' THEN s.score ELSE NULL END) AS 'ចំរៀង',
    MAX(CASE WHEN s.subject_name = 'សិល្បៈ' THEN s.score ELSE NULL END) AS 'សិល្បៈ',
    MAX(CASE WHEN s.subject_name = 'កីឡា' THEN s.score ELSE NULL END) AS 'កីឡា',
    MAX(CASE WHEN s.subject_name = 'បំណិនជីវិត' THEN s.score ELSE NULL END) AS 'បំណិនជីវិត'
FROM 
    student_subjects s
JOIN 
    rankings r ON s.student_id = r.student_id 
    AND s.monthly_id = r.monthly_id 
    AND s.class_id = r.class_id
GROUP BY 
    s.student_id, s.class_id, s.monthly_id, s.student_name, s.class_name, s.month_name,
    r.subjects_count, r.total_score, r.average_score, r.rank_in_class, r.class_size; 