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
    sms.score,
    st.year_study_id,
    ys.year_study
FROM
    tbl_student_info s
    INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.isDeleted = 0
    INNER JOIN tbl_classroom c ON st.class_id = c.class_id
    INNER JOIN tbl_year_study ys ON st.year_study_id = ys.year_study_id
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
        score,
        year_study_id,
        year_study
    FROM 
        view_student_monthly_score_detail
),
student_aggregates AS (
    SELECT
        student_id,
        class_id,
        monthly_id,
        year_study_id,
        year_study,
        COUNT(DISTINCT subject_name) AS subjects_count,
        SUM(score) AS total_score,
        AVG(score) AS average_score
    FROM
        student_subjects
    GROUP BY
        student_id, class_id, monthly_id, year_study_id, year_study
),
student_rankings AS (
    SELECT
        sa.*,
        RANK() OVER (
            PARTITION BY class_id, monthly_id, year_study_id 
            ORDER BY average_score DESC
        ) AS rank_in_class,
        COUNT(*) OVER (
            PARTITION BY class_id, monthly_id, year_study_id
        ) AS class_size
    FROM
        student_aggregates sa
)
SELECT
    sr.*,
    ss.subject_name,
    ss.score AS subject_score
FROM
    student_rankings sr
    LEFT JOIN student_subjects ss ON 
        sr.student_id = ss.student_id 
        AND sr.class_id = ss.class_id 
        AND sr.monthly_id = ss.monthly_id
        AND sr.year_study_id = ss.year_study_id;

-- Create a view for semester scores
CREATE OR REPLACE VIEW view_student_semester_score_detail AS
SELECT
    s.student_id,
    s.student_name,
    c.class_id,
    c.class_name,
    sem.semester_id,
    sem.semester_name,
    sub.subject_code,
    sub.subject_name,
    sss.score,
    st.year_study_id,
    ys.year_study
FROM
    tbl_student_info s
    INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.isDeleted = 0
    INNER JOIN tbl_classroom c ON st.class_id = c.class_id
    INNER JOIN tbl_year_study ys ON st.year_study_id = ys.year_study_id
    INNER JOIN tbl_semester_exam_subjects ses ON c.class_id = ses.class_id
    INNER JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
    INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
    INNER JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
    LEFT JOIN tbl_student_semester_score sss ON 
        s.student_id = sss.student_id 
        AND sss.semester_exam_subject_id = ses.id
        AND sss.isDeleted = 0
WHERE
    s.isDeleted = 0
    AND ses.isDeleted = 0;

-- Create a view for semester averages
CREATE OR REPLACE VIEW view_student_semester_averages AS
WITH monthly_averages AS (
    SELECT
        s.student_id,
        s.student_name,
        c.class_id,
        c.class_name,
        sem.semester_id,
        sem.semester_name,
        st.year_study_id,
        ys.year_study,
        AVG(sms.score) AS monthly_avg
    FROM
        tbl_student_info s
        INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.isDeleted = 0
        INNER JOIN tbl_classroom c ON st.class_id = c.class_id
        INNER JOIN tbl_year_study ys ON st.year_study_id = ys.year_study_id
        INNER JOIN classroom_subject_monthly_score csms ON c.class_id = csms.class_id
        INNER JOIN tbl_semester_exam_subjects ses ON 
            c.class_id = ses.class_id 
            AND csms.assign_subject_grade_id = ses.assign_subject_grade_id
        INNER JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
        LEFT JOIN tbl_student_monthly_score sms ON 
            s.student_id = sms.student_id 
            AND sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            AND sms.isDeleted = 0
        WHERE
            s.isDeleted = 0
            AND csms.isDeleted = 0
            AND FIND_IN_SET(csms.monthly_id, ses.monthly_ids)
        GROUP BY
            s.student_id, s.student_name, c.class_id, c.class_name, 
            sem.semester_id, sem.semester_name, st.year_study_id, ys.year_study
),
exam_averages AS (
    SELECT
        s.student_id,
        s.student_name,
        c.class_id,
        c.class_name,
        sem.semester_id,
        sem.semester_name,
        st.year_study_id,
        ys.year_study,
        AVG(sss.score) AS exam_avg
    FROM
        tbl_student_info s
        INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.isDeleted = 0
        INNER JOIN tbl_classroom c ON st.class_id = c.class_id
        INNER JOIN tbl_year_study ys ON st.year_study_id = ys.year_study_id
        INNER JOIN tbl_semester_exam_subjects ses ON c.class_id = ses.class_id
        INNER JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
        LEFT JOIN tbl_student_semester_score sss ON 
            s.student_id = sss.student_id 
            AND sss.semester_exam_subject_id = ses.id
            AND sss.isDeleted = 0
        WHERE
            s.isDeleted = 0
            AND ses.isDeleted = 0
        GROUP BY
            s.student_id, s.student_name, c.class_id, c.class_name,
            sem.semester_id, sem.semester_name, st.year_study_id, ys.year_study
)
SELECT
    ma.student_id,
    ma.student_name,
    ma.class_id,
    ma.class_name,
    ma.semester_id,
    ma.semester_name,
    ma.year_study_id,
    ma.year_study,
    ma.monthly_avg,
    ea.exam_avg,
    ROUND((COALESCE(ma.monthly_avg, 0) + COALESCE(ea.exam_avg, 0)) / 2, 2) AS final_semester_avg
FROM
    monthly_averages ma
    LEFT JOIN exam_averages ea ON 
        ma.student_id = ea.student_id 
        AND ma.class_id = ea.class_id
        AND ma.semester_id = ea.semester_id
        AND ma.year_study_id = ea.year_study_id;

