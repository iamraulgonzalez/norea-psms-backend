DELIMITER $$

DROP PROCEDURE IF EXISTS `create_dynamic_score_pivot_view` $$
CREATE PROCEDURE `create_dynamic_score_pivot_view`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE subject_columns TEXT DEFAULT '';
    DECLARE subject_name VARCHAR(255);
    DECLARE subject_id INT;
    DECLARE subject_cursor CURSOR FOR 
        SELECT subject_id, subject_name 
        FROM tbl_subject 
        WHERE isDeleted = 0 
        ORDER BY subject_name;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Start building the dynamic SQL
    SET @sql = 'DROP VIEW IF EXISTS view_student_monthly_score_pivot;
    CREATE VIEW view_student_monthly_score_pivot AS
    WITH student_scores AS (
        SELECT 
            si.student_id,
            si.student_name,
            c.class_id,
            c.class_name,
            m.monthly_id,
            m.month_name,
            sub.subject_id,
            sub.subject_name,
            sms.score
        FROM tbl_student_info si
        INNER JOIN tbl_study st ON si.student_id = st.student_id AND st.status = "active" AND st.isDeleted = 0
        INNER JOIN tbl_classroom c ON st.class_id = c.class_id
        INNER JOIN classroom_subject_monthly_score csms ON c.class_id = csms.class_id
        INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
        INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
        INNER JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
        LEFT JOIN tbl_student_monthly_score sms ON (
            si.student_id = sms.student_id 
            AND sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            AND sms.isDeleted = 0
        )
        WHERE si.isDeleted = 0
        AND csms.isDeleted = 0
    ),
    student_totals AS (
        SELECT 
            student_id,
            student_name,
            class_id,
            class_name,
            monthly_id,
            month_name,
            COUNT(DISTINCT subject_id) AS subjects_count,
            SUM(COALESCE(score, 0)) AS total_score,
            AVG(score) AS average_score
        FROM student_scores
        GROUP BY 
            student_id, 
            student_name,
            class_id,
            class_name,
            monthly_id,
            month_name
    ),
    student_rankings AS (
        SELECT 
            st.*,
            RANK() OVER (PARTITION BY class_id, monthly_id ORDER BY average_score DESC) AS rank_in_class,
            COUNT(*) OVER (PARTITION BY class_id, monthly_id) AS class_size
        FROM student_totals st
    )
    SELECT 
        sr.student_id,
        sr.student_name,
        sr.class_id,
        sr.class_name,
        sr.monthly_id,
        sr.month_name,
        sr.subjects_count,
        sr.total_score,
        sr.average_score,
        sr.rank_in_class,
        sr.class_size';
    
    -- Open cursor and build dynamic subject columns
    OPEN subject_cursor;
    SET done = FALSE;
    read_loop: LOOP
        FETCH subject_cursor INTO subject_id, subject_name;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Use plain column names without special characters for reliability
        SET subject_columns = CONCAT(subject_columns, ',
        MAX(CASE WHEN ss.subject_id = ', subject_id, ' THEN ss.score END) AS subject_', subject_id);
    END LOOP;
    CLOSE subject_cursor;
    
    -- Complete the SQL statement with the subject columns and GROUP BY
    SET @sql = CONCAT(@sql, subject_columns, '
    FROM student_rankings sr
    LEFT JOIN student_scores ss ON (
        sr.student_id = ss.student_id 
        AND sr.class_id = ss.class_id 
        AND sr.monthly_id = ss.monthly_id
    )
    GROUP BY 
        sr.student_id,
        sr.student_name,
        sr.class_id,
        sr.class_name,
        sr.monthly_id,
        sr.month_name,
        sr.subjects_count,
        sr.total_score,
        sr.average_score,
        sr.rank_in_class,
        sr.class_size
    ORDER BY sr.class_id, sr.monthly_id, sr.rank_in_class');
    
    -- Execute the dynamic SQL
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END $$

DELIMITER ;

-- Execute the procedure to create the view
CALL create_dynamic_score_pivot_view(); 