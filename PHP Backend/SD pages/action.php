<?php
include '../../config.php';

if (isset($_GET['EditschedID'])) {

    $schedID = $_GET['EditschedID'];
    $fetchSched = $con->prepare("SELECT schedule_time_start, schedule_time_end, schedule_day, teacher_id, class_id, subject_id, room_id FROM schedule_tb WHERE schedule_id = ?");
    $fetchSched->bind_param("i", $schedID);
    $fetchSched->execute();
    $fetchSched->store_result();
    $fetchSched->bind_result($schedule_time_start, $schedule_time_end, $schedule_day, $teacher_id, $class_id, $subject_id, $room_id);
    $fetchSched->fetch();

    // Fetch the data for Teacher
    $fetchTeacher = $con->prepare("SELECT teacher_name FROM teacher_tb WHERE teacher_id = ?");
    $fetchTeacher->bind_param("i", $teacher_id);
    $fetchTeacher->execute();
    $fetchTeacher->store_result();
    $fetchTeacher->bind_result($teacher_name);
    $fetchTeacher->fetch();

    // Fetch the data for Class
    $fetchclass = $con->prepare("SELECT class_courseStrand, class_year, class_section, class_department FROM class_tb WHERE class_id = ?");
    $fetchclass->bind_param("i", $class_id);
    $fetchclass->execute();
    $fetchclass->store_result();
    $fetchclass->bind_result($class_courseStrand, $class_year, $class_section, $class_department);
    $fetchclass->fetch();

    $nameFetchClassResult = $class_courseStrand . ' ' . $class_year . ' - ' . $class_section;

    // Fetch the data for Subject
    $fetchSubject = $con->prepare("SELECT subject_name, subject_description FROM subject_tb WHERE subject_id = ?");
    $fetchSubject->bind_param("i", $subject_id);
    $fetchSubject->execute();
    $fetchSubject->store_result();
    $fetchSubject->bind_result($subject_name, $subject_description);
    $fetchSubject->fetch();


?>
    <form action="" method="post">
        <input type="hidden" name="schedID" id="" value="<?php echo $_GET['EditschedID']; ?>">
        <label for="">Time:</label>
        <input type="time" name="new-time-start" id="" value="<?php echo $schedule_time_start; ?>">
        <input type="time" name="new-time-end" id="" value="<?php echo $schedule_time_end; ?>">
        <br>
        <!-- <label for="">Day:</label><br>
        <input type="checkbox" name="day[]" id="" value="Monday" <?php //echo $schedule_day == 'Monday' ? 'checked' : ''; 
                                                                    ?>><label for="">Monday</label><br>
        <input type="checkbox" name="day[]" id="" value="Tuesday" <?php //echo $schedule_day == 'Tuesday' ? 'checked' : ''; 
                                                                    ?>><label for="">Tuesday</label><br>
        <input type="checkbox" name="day[]" id="" value="Wednesday" <?php //echo $schedule_day == 'Wednesday' ? 'checked' : ''; 
                                                                    ?>><label for="">Wednesday</label><br>
        <input type="checkbox" name="day[]" id="" value="Thursday" <?php //echo $schedule_day == 'Thursday' ? 'checked' : ''; 
                                                                    ?>><label for="">Thursday</label><br>
        <input type="checkbox" name="day[]" id="" value="Friday" <?php //echo $schedule_day == 'Friday' ? 'checked' : ''; 
                                                                    ?>><label for="">Friday</label><br>
        <input type="checkbox" name="day[]" id="" value="Saturday" <?php //echo $schedule_day == 'Saturday' ? 'checked' : ''; 
                                                                    ?>><label for="">Saturday</label><br> -->


        <label for="">Program:</label>

        <select name="classID">
            <?php
            $fetchclasses = $con->query("SELECT * FROM class_tb");
            while ($row = $fetchclasses->fetch_assoc()) {
                $nameFetchClassResults = $row['class_courseStrand'] . ' ' . $row['class_year'] . ' - ' . $row['class_section'];

            ?>
                <option value="<?php echo $row['class_id'] ?>" <?php echo $nameFetchClassResult == $nameFetchClassResults ? 'selected' : ''; ?>><?php echo $nameFetchClassResults ?></option>
            <?php
            }
            ?>
        </select>

        <label for="">Teacher:</label>
        <select name="teacherID" id="">
            <?php
            $fetchTeachers = $con->query("SELECT * FROM teacher_tb WHERE `status` = 1");
            while ($row = $fetchTeachers->fetch_assoc()) {
            ?>
                <option value="<?php echo $row['teacher_id'] ?>" <?php echo $teacher_name == $row['teacher_name'] ? 'selected' : ''; ?>><?php echo $row['teacher_name'] ?></option>
            <?php
            }
            ?>
        </select>
        <br>
        <label for="">Subject:</label>
        <select name="subjectID" id="">
            <?php
            $fetchSubjects = $con->query("SELECT * FROM subject_tb");
            //WHERE subject_department = '$class_department' OR subject_department = 'General'
            while ($row = $fetchSubjects->fetch_assoc()) {
            ?>
                <option value="<?php echo $row['subject_id']; ?>" <?php echo $subject_name == $row['subject_name'] ? 'selected' : ''; ?>><?php echo $row['subject_name']; ?></option>
            <?php
            }

            ?>
            <input type="submit" name="update" id="">
        </select>
    </form>
<?php
} else if (isset($_GET['DelschedID'])) {
    $schedID = $_GET['DelschedID'];
    $delSched = $con->prepare("DELETE FROM schedule_tb WHERE schedule_id = ?");
    $delSched->bind_param("i", $schedID);
    if ($delSched->execute()) {
        echo "<script>alert('Deleted Successfully'); window.location.href = '../../admin-view-class-schedule.php';</script>";
        exit;
    }
}

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if (isset($_POST['update'])) {

    $schedID = $_POST['schedID'];
    $new_time_start = $_POST['new-time-start'];
    $new_time_end = $_POST['new-time-end'];
    // $day = $_POST['day'];
    // $days = isset($_POST['day']) && is_array($_POST['day']) ? $_POST['day'] : [];
    $classID = $_POST['classID'];
    $teacherID = $_POST['teacherID'];
    $subjectID = $_POST['subjectID'];

    // foreach ($days as $day) {

    $updateSched = $con->prepare("UPDATE schedule_tb SET schedule_time_start =?, schedule_time_end=? , teacher_id=?, class_id=?, subject_id=? WHERE schedule_id = ?");
    $updateSched->bind_param("ssiiii", $new_time_start, $new_time_end, $teacherID, $classID, $subjectID, $schedID);
    $updateSched->execute();
    // }
    echo "<script>alert('Updated Successfully'); window.location.href = '../../admin-view-class-schedule.php';</script>";

    // header('Location : ');
}
// }
