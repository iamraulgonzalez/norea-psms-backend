

create view StudentMonthlyScoreDetail as
select 
    si.student_id,
    si.student_name,
    c.class_id,
    c.class_name,
    m.monthly_id,
    m.month_name,
    sub.subject_id,
    sub.subject_name,
    sms.score
from tbl_student_info si
inner join tbl_study st on si.student_id = st.student_id and st.status = "active" and st.isDeleted = 0
inner join tbl_classroom c on st.class_id = c.class_id
inner join tbl_monthly m on sms.monthly_id = m.monthly_id
inner join tbl_subject sub on sms.subject_id = sub.subject_id
inner join tbl_student_monthly_score sms on si.student_id = sms.student_id and sms.monthly_id = m.monthly_id and sms.class_id = c.class_id
where si.isDeleted = 0 and sms.isDeleted = 0

