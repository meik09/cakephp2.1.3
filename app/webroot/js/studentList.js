$(document).ready(function() {
    $("#find_add_us, #find_add_gs").click(function() {
        if ($("#find_add_us:checked").val() == "1" || $("#find_add_gs:checked").val() == "1") {
            $(".student_find_tbl2").css("display", "");
        } else {
            $(".student_find_tbl2").css("display", "none");
            $("#vmoStudentStudentNo").val("");
            $("#vmoStudentStudentStatus").val("");
            $("#vmoStudentGraduationDate").val("");
            $("#vmoStudentTeacherName").val("");
            $("#vmoStudentClubName").val("");
        }
    });

    $("#find_add_hs").click(function() {
        if ($("#find_add_hs:checked").val() == "1") {
            $(".student_find_tbl3").css("display", "");
        } else {
            $(".student_find_tbl3").css("display", "none");
            $("#vmoStudentHsFacultyName").val("");
            $("#vmoStudentHsTeacherName").val("");
            $("#vmoStudentHsClubName").val("");
        }
    });

    $("#find_add_jh").click(function() {
        if ($("#find_add_jh:checked").val() == "1") {
            $(".student_find_tbl4").css("display", "");
        } else {
            $(".student_find_tbl4").css("display", "none");
            $("#vmoStudentJhFacultyName").val("");
            $("#vmoStudentJhTeacherName").val("");
            $("#vmoStudentJhClubName").val("");
        }
    });    
});