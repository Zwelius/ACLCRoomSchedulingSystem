<!DOCTYPE html>
<html lang="en">
<style>
    .error {
        color: red;
        /* display: none; */
    }
</style>

<body>
    <div class="main">
        <div class="top" class="side-by-side">
            <form action="?action=form1" method="post" class="form side-by-side">
                <div>
                    <label>Standing</label><br>
                    <input type="radio" name="standing" value="College" <?php if (isset($_POST['sub2']) || isset($_POST['sub1']) || isset($_POST['sub3']) || isset($_POST['sub4'])) {
                                                                            echo ($_POST['standing'] == 'College') ? "checked" : "";
                                                                        } ?> required>
                    <label style="margin-right: 10px">College</label>

                    <input type="radio" name="standing" value="SHS" <?php if (isset($_POST['sub2']) || isset($_POST['sub1']) || isset($_POST['sub3']) || isset($_POST['sub4'])) {
                                                                        echo ($_POST['standing'] == 'SHS') ? "checked" : "";
                                                                    } ?> required>
                    <label>SHS</label>
                    <input type="submit" name="sub1" value="Next">
                </div>
            </form>

            <?php
            if (isset($_GET['action']) && ($_GET['action'] == "form1" || $_GET['action'] == 'form2' || $_GET['action'] == "form3" || $_GET['action'] == 'form4')) {
                include 'config.php';
                $result = findStandingForClass($con);
            ?>
                <form action="?action=form2" method="post" class="side-by-side">
                    <div>
                        <input type="hidden" value="<?php echo $_POST['standing']; ?>" name="standing">
                        <label>School Year</label><br>
                        <select name="AY" id="yearSelect">
                            <?php
                            $currentYear = date("Y");
                            for ($i = -1; $i < 4; $i++) {
                                $year = $currentYear + $i;
                                $nextYear = $year + 1;
                                $optionValue = $year . "-" . $nextYear;
                            ?>
                                <option value=<?php echo $optionValue ?> <?php if (isset($_POST['sub2']) || isset($_POST['sub3']) || isset($_POST['sub4'])) {
                                                                                echo ($_POST['AY'] == "$optionValue") ? "selected" : "";
                                                                            } ?> required><?php echo $optionValue ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label>Semester</label><br>
                        <div>
                            <input type="radio" name="SetSem" id="firstSemester" value="1st" <?php if (isset($_POST['sub2']) || isset($_POST['sub3']) || isset($_POST['sub4'])) {
                                                                                                    echo ($_POST['SetSem'] == '1st') ? "checked" : "";
                                                                                                } ?> required>
                            <label>1st</label>

                            <input type="radio" name="SetSem" id="secondSemester" value="2nd" <?php if (isset($_POST['sub2']) || isset($_POST['sub3']) || isset($_POST['sub4'])) {
                                                                                                    echo ($_POST['SetSem'] == '2nd') ? "checked" : "";
                                                                                                } ?> required>
                            <label>2nd</label>
                        </div>
                    </div>

                    <div>
                        <label>Section</label><br>
                        <select name="class" id="class-select" required>
                            <?php
                            if ($result->num_rows > 0) {

                                while ($row = mysqli_fetch_array($result)) {
                                    $classID = $row['class_id'];
                                    $courseStrand = $row['class_courseStrand'];
                                    $year = $row['class_year'];
                                    $section = $row['class_section'];
                                    $departmentUnder = $row['class_department'];

                                    $className = $courseStrand . $year . '-' . $section;
                                    $classID_name = $classID . '|' . $className;
                                    $classID_name = htmlspecialchars($classID_name);
                            ?>
                                    <option value='<?php echo $classID_name; ?>' <?php if (isset($_POST['sub2']) || isset($_POST['sub3']) || isset($_POST['sub4'])) {
                                                                                        echo ($_POST['class'] == "$classID_name") ? "selected" : "";
                                                                                    } ?>><?php echo $className; ?></option>
                                <?php
                                }
                            } else {
                                ?>
                                <option value="" id="class-option" disabled selected>Pls create a class/section</option>

                            <?php
                            }

                            ?>
                        </select>
                        <span id="class-error" class="error" style="display:none;">Pls create a class/section</span>

                        <input type="submit" name="sub2" value="Next" id="sub2">
                    </div>
                </form>

                <?php
                if (isset($_POST['sub2']) || $_GET['action'] == "form3" || $_GET['action'] == 'form4') {
                ?>
                    <form action="?action=form3" method="post" class="side-by-side" id="select-subject-teacher-form">
                        <input type="hidden" value="<?php echo $_POST['standing']; ?>" name="standing">
                        <input type="hidden" name="AY" value="<?php echo $_POST['AY'] ?>">
                        <input type="hidden" name="SetSem" value="<?php echo $_POST['SetSem'] ?>">
                        <input type="hidden" name="class" value="<?php echo $_POST['class'] ?>">

                        <div>
                            <label>Choose a Subject</label><br>
                            <datalist id="subject-list">
                                <?php
                                $findsubjects = $con->prepare("SELECT * FROM subject_tb");
                                $findsubjects->execute();
                                $resultSubjects = $findsubjects->get_result();
                                if ($resultSubjects->num_rows > 0) {

                                    while ($rowsubjects = $resultSubjects->fetch_assoc()) {
                                ?>
                                        <option value="<?php echo $rowsubjects['subject_id'] . '|' . $rowsubjects['subject_name']; ?>">
                                            <?php echo $rowsubjects['subject_name']; ?>
                                        </option>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <option value="Pls create a subject" disabled></option>
                                <?php

                                }

                                $findsubjects->free_result();
                                ?>
                            </datalist>
                            <input type="text" name="subject" id="selected-subject" list="subject-list" value="<?php if (isset($_POST['sub3'])) :
                                                                                                                    echo $_POST['subject'];
                                                                                                                endif; ?>" required>
                        </div>

                        <div>
                            <label>Teacher</label><br>
                            <datalist id="teacher-list">
                                <?php
                                $findTechers = $con->prepare("SELECT * FROM teacher_tb WHERE `status` = 1");
                                $findTechers->execute();
                                $resultTeachers = $findTechers->get_result();
                                if ($resultTeachers->num_rows > 0) {

                                    while ($rowTeacher = $resultTeachers->fetch_assoc()) {
                                ?>
                                        <option value="<?php echo $rowTeacher['teacher_id'] . '|' . $rowTeacher['teacher_name']; ?>">
                                            <?php echo $rowTeacher['teacher_name']; ?>
                                        </option>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <option value="" disabled selected>Pls create a teacher</option>
                                <?php
                                }
                                ?>
                            </datalist>

                            <input type="text" name="teacher" id="selected-teacher" list="teacher-list" placeholder="Choose a teacher..." value="<?php if (isset($_POST['sub3']) || isset($_POST['sub4'])) :
                                                                                                                                                        echo $_POST['teacher'];
                                                                                                                                                    endif; ?>" required>
                            <input type="submit" name="sub3" id="sub3" value="Add">
                        </div>
                    </form>
        </div>
    </div>

    <?php
                    if (isset($_POST['sub3']) || $_GET['action'] == 'form4') {
                        $returnExistingSubjectForClass = ExistingSubjectForClass($con); // returns as array [className, subjectID, subjectName, true || false] boolean is if class already have the subject inputed

                        // if ($returnExistingSubjectForClass['exist']) {
                        //     echo "<script>alert('Class already have this subject!')</script>";
                        // } else {
    ?>

        <main class="form-4">
            <form action="?action=form4" method="post" id="select-room-form">
                <input type="hidden" value="<?php echo $_POST['standing']; ?>" name="standing">
                <input type="hidden" name="AY" value="<?php echo $_POST['AY'] ?>">
                <input type="hidden" name="SetSem" value="<?php echo $_POST['SetSem'] ?>">
                <input type="hidden" name="class" value="<?php echo $_POST['class'] ?>">
                <input type="hidden" name="subject" value="<?php echo $_POST['subject'] ?>">
                <input type="hidden" name="teacher" value="<?php echo $_POST['teacher'] ?>">

                <table>
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Time</th>
                            <th>Days</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <datalist id="room-list">
                                    <?php
                                    $findeRooms = $con->prepare("SELECT room_id, room_name, room_type FROM room_tb WHERE room_type = (SELECT subject_type FROM subject_tb WHERE subject_id = ?)");
                                    $findeRooms->bind_param("i", $returnExistingSubjectForClass['subjectID']);
                                    $findeRooms->execute();
                                    $resultRooms = $findeRooms->get_result();
                                    $roomType = ''; // Initialize roomType variable
                                    if ($resultRooms->num_rows > 0) {

                                        while ($rowRoom = $resultRooms->fetch_assoc()) {
                                            $roomType = $rowRoom['room_type'];
                                    ?>
                                            <option value="<?php echo $rowRoom['room_id'] . '|' . $rowRoom['room_name']; ?>">
                                                <?php echo $rowRoom['room_name']; ?>
                                            </option>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <option value="Pls create a room" disabled selected></option>
                                    <?php
                                    }

                                    ?>
                                </datalist>
                                <input type="text" name="room" id="selected-room" list="room-list" placeholder="Select a room..." class="<?php echo htmlspecialchars($roomType); ?>">
                            </td>
                            <td>
                                <div class="time">
                                    <label for="">Start: </label><input type="time" name="schedule_time[start]" id="startTime" min="07:00" max="22:00"><br>
                                    <label for="">End: </label><input type="time" name="schedule_time[end]" id="endTime" min="07:00" max="22:00">
                                </div>

                            </td>
                            <td>
                                <input type="checkbox" name="days[]" value="Monday">Monday<br>
                                <input type="checkbox" name="days[]" value="Tuesday">Tuesday<br>
                                <input type="checkbox" name="days[]" value="Wednesday">Wednesday<br>
                                <input type="checkbox" name="days[]" value="Thursday">Thursday<br>
                                <input type="checkbox" name="days[]" value="Friday">Friday<br>
                                <input type="checkbox" name="days[]" value="Saturday">Saturday<br>
                            </td>
                            <td><input type="submit" name="sub4"></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </main>

    <?php
                        if (isset($_POST['sub4']) || $_GET['action'] == 'form4') {
                            // Sanitize and validate inputs
                            if (!isset($_POST['room'], $_POST['AY'], $_POST['SetSem'], $_POST['schedule_time'], $_POST['days'], $_POST['class'], $_POST['teacher'], $_POST['subject'])) {
                                echo "<script>alert('Missing required fields.');</script>";
                                exit;
                            }

                            $roomID_name = htmlspecialchars($_POST['room']);
                            list($roomID, $roomName) = explode('|', $roomID_name);
                            $roomID = htmlspecialchars($roomID);
                            $roomName = htmlspecialchars($roomName);

                            // Prepare the statement to find occupied rooms
                            $findOccupiedRoom = $con->prepare("SELECT * FROM schedule_tb WHERE room_id = ? AND schedule_SY = ? AND schedule_semester = ?");
                            $findOccupiedRoom->bind_param("iss", $roomID, $_POST['AY'], $_POST['SetSem']);
                            $findOccupiedRoom->execute();
                            $resultfindOccupiedRoom = $findOccupiedRoom->get_result();

                            $submittedTimeStart = strtotime(htmlspecialchars($_POST['schedule_time']['start']));
                            $submittedTimeEnd = strtotime(htmlspecialchars($_POST['schedule_time']['end']));
                            $submittedDays = isset($_POST['days']) && is_array($_POST['days']) ? $_POST['days'] : [];

                            $isConflict = false;

                            // Check for conflicts with existing room schedules
                            while ($rowfindOccupiedRoom = $resultfindOccupiedRoom->fetch_assoc()) {
                                $occupiedRoomTimeStart = strtotime($rowfindOccupiedRoom['schedule_time_start']);
                                $occupiedRoomTimeEnd = strtotime($rowfindOccupiedRoom['schedule_time_end']);
                                $occupiedRoomDay = $rowfindOccupiedRoom['schedule_day'];

                                // Check for overlapping days
                                if (in_array($occupiedRoomDay, $submittedDays)) {
                                    // Check for overlapping times within those overlapping days
                                    if (($submittedTimeStart < $occupiedRoomTimeEnd) && ($submittedTimeEnd > $occupiedRoomTimeStart)) {
                                        $isConflict = true;
                                        break;
                                    }
                                }
                            }

                            $classID_name = htmlspecialchars($_POST['class']);
                            list($classID, $className) = explode('|', $classID_name);
                            $classID = htmlspecialchars($classID);
                            $className = htmlspecialchars($className);

                            // Prepare the statement to find conflicting class schedules
                            $findConflictSchedule = $con->prepare("SELECT * FROM schedule_tb WHERE class_id = ? AND schedule_SY = ? AND schedule_semester = ?");
                            $findConflictSchedule->bind_param("iss", $classID, $_POST['AY'], $_POST['SetSem']);
                            $findConflictSchedule->execute();
                            $resultfindConflictSchedule = $findConflictSchedule->get_result();

                            // Check for conflicts with existing class schedules
                            while ($rowfindConflictSchedule = $resultfindConflictSchedule->fetch_assoc()) {
                                $occupiedClassTimeStart = strtotime($rowfindConflictSchedule['schedule_time_start']);
                                $occupiedClassTimeEnd = strtotime($rowfindConflictSchedule['schedule_time_end']);
                                $occupiedClassDay = $rowfindConflictSchedule['schedule_day'];

                                if (in_array($occupiedClassDay, $submittedDays)) {
                                    // Check for overlapping times within those overlapping days
                                    if (($submittedTimeStart < $occupiedClassTimeEnd) && ($submittedTimeEnd > $occupiedClassTimeStart)) {
                                        $isConflict = true;
                                        break;
                                    }
                                }
                            }

                            if ($isConflict) {
                                echo "<script>alert('The submitted schedule conflicts with an existing schedule.');</script>";
                            } else {

                                $teacherID_name = htmlspecialchars($_POST['teacher']);
                                list($teacherID, $teacherName) = explode('|', $teacherID_name);
                                $teacherID = htmlspecialchars($teacherID);
                                $teacherName = htmlspecialchars($teacherName);

                                $subjectID_name = htmlspecialchars($_POST['subject']);
                                list($subjectID, $subjectName) = explode('|', $subjectID_name);
                                $subjectID = htmlspecialchars($subjectID);
                                $subjectName = htmlspecialchars($subjectName);

                                foreach ($submittedDays as $day) {
                                    $insertSchedule = $con->prepare("INSERT INTO schedule_tb (schedule_time_start, schedule_time_end, schedule_day, schedule_semester, schedule_SY, teacher_id, class_id, subject_id, room_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                    $insertSchedule->bind_param("ssssssiii", $_POST['schedule_time']['start'], $_POST['schedule_time']['end'], $day, $_POST['SetSem'], $_POST['AY'], $teacherID, $classID, $subjectID, $roomID);
                                    $insertSchedule->execute();
                                }
                            }
                        }
                        // }
                    }
                    $schedules = ClassExistingSubjectOfTheSYandSem($con);
    ?>

    <table>
        <thead>
            <tr>
                <th>Subject Name</th>
                <th>Time</th>
                <th>Room</th>
                <th>Teacher</th>
                <th>Days</th>

            </tr>
        </thead>
        <tbody>
            <?php
                    if (!empty($schedules)) {
                        foreach ($schedules as $schedule) {
            ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['schedule_time_start']) . '<br>' . htmlspecialchars($schedule['schedule_time_end']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['room_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['teacher_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['schedule_day']); ?></td>
                    </tr>
                <?php
                        } ?>
        </tbody>
    </table>
<?php

                    } else {
                        echo "<h1 style='color:red'>No Subject Found</h1>";
                    }
                }

?>

<?php

            }

?>
</div>
<script>
    function checkClasses() {
        var selectClass = document.getElementById('class-select');
        // var classOption = document.getElementById('class-option');
        var classError = document.getElementById('class-error');
        var submitButton = document.getElementById('sub2');
        if (selectClass.options.length === 1 && selectClass.options[0].value === "") {
            submitButton.disabled = true;
            // selectClass.style.color = 'red';
            classError.style.display = 'none';
        } else {
            submitButton.disabled = false;
        }
    }

    window.onload = function() {
        checkClassesAndSubmit(); // Check classes on page load
    }

    function checkSubjects() {
        var subjectInput = document.getElementById('selected-subject');
        var subjectDatalist = document.getElementById('subject-list');

        var teacherInput = document.getElementById('selected-teacher');
        var teacherDatalist = document.getElementById('teacher-list');

        var submitButton = document.getElementById('sub3');

        var noSubjects = subjectDatalist.options.length === 0 ||
            (subjectDatalist.options.length === 1 && subjectDatalist.options[0].disabled);

        var noTeachers = teacherDatalist.options.length === 0 ||
            (teacherDatalist.options.length === 1 && teacherDatalist.options[0].disabled);

        if (noSubjects) {
            subjectInput.disabled = true;
            subjectInput.value = 'Pls create a subject';
            subjectInput.style.color = 'red';
        } else {
            subjectInput.disabled = false;
        }

        if (noTeachers) {
            teacherInput.disabled = true;
            teacherInput.value = 'No teachers is available';
            teacherInput.style.color = 'red';

        } else {
            teacherInput.disabled = false;
        }

        submitButton.disabled = noSubjects || noTeachers;
    }
    window.onload = function() {
        checkSubjects();
    }

    function checkRoom() {
        var roomInput = document.getElementById('selected-room');
        var roomDatalist = document.getElementById('room-list');
        var submitButton = document.getElementById('sub4');

        var noRooms = roomDatalist.options.length === 0 ||
            (roomDatalist.options.length === 1 && roomDatalist.options[0].disabled);

        if (noRooms) {
            roomInput.disabled = true;
            roomInput.value = 'Pls create a room';
            roomInput.style.color = 'red';
        } else {
            roomInput.disabled = false;
        }


    }
    window.onload = function() {
        checkRoom();
    }


    document.getElementById('select-subject-teacher-form').addEventListener('submit', function(event) {
        var inputSubject = document.getElementById('selected-subject');
        var datalistSubject = document.getElementById('subject-list');
        var optionsSubject = datalistSubject.options;
        var inputValueSubject = inputSubject.value;

        var inputTeacher = document.getElementById('selected-teacher');
        var datalistTeacher = document.getElementById('teacher-list');
        var optionsTeacher = datalistTeacher.options;
        var inputValueTeacher = inputTeacher.value;

        var isValidSubject = false;
        var isValidTeacher = false;

        for (var i = 0; i < optionsSubject.length; i++) {
            if (inputValueSubject === optionsSubject[i].value) {
                isValidSubject = true;
                break;
            }
        }

        for (var i = 0; i < optionsTeacher.length; i++) {
            if (inputValueTeacher === optionsTeacher[i].value) {
                isValidTeacher = true;
                break;
            }
        }

        if (!isValidSubject) {
            alert('Please select a valid subject from the list.');
            event.preventDefault(); // Prevent the form from submitting
            return;
        }

        if (!isValidTeacher) {
            alert('Please select a valid teacher from the list.');
            event.preventDefault(); // Prevent the form from submitting
            return;
        }
    });

    document.getElementById('select-room-form').addEventListener('submit', function(event) {
        var inputRoom = document.getElementById('selected-room');
        var datalistRoom = document.getElementById('room-list');
        var optionsRoom = datalistRoom.options;
        var inputValueRoom = inputRoom.value;
        var isValidRoom = false;

        for (var i = 0; i < optionsRoom.length; i++) {
            if (inputValueRoom === optionsRoom[i].value) {
                isValidRoom = true;
                break;
            }
        }

        if (!isValidRoom) {
            alert('Please select a valid room from the list.');
            event.preventDefault(); // Prevent the form from submitting
            return;
        }

    });
</script>
<script>
    // Get references to the start and end time input fields
    const startTimeInput = document.getElementById('startTime');
    const endTimeInput = document.getElementById('endTime');
    const selectedRoom = document.getElementById('selected-room');
    const roomType = selectedRoom.className;

    // Add event listener to the start time input field
    startTimeInput.addEventListener('change', function() {
        // Get the value of the start time input field
        const startTimeValue = startTimeInput.value;

        // Parse the start time string into hours and minutes
        const [startHour, startMinute] = startTimeValue.split(':').map(Number);

        // Calculate the end time by adding 1 hour and 30 minutes to the start time
        let endHour = (startHour + 1) % 24;
        let endMinute = '';

        // Determine end minutes based on room type
        if (roomType === "lecture") {
            endMinute = (startMinute + 60) % 60;
        } else if (roomType === "Laboratory") {
            endMinute = (startMinute + 90) % 60;
            endHour += Math.floor((startMinute + 30) / 60); // Add extra hour if minutes exceed 60
        }

        // Format the end time
        const endTimeValue = `${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}`;

        // Update the value of the end time input field
        endTimeInput.value = endTimeValue;
    });
</script>
</body>

</html>

<?php
function findStandingForClass($con)
{
    $standing = $_POST['standing'];
    $sql = $con->prepare("SELECT * FROM class_tb WHERE class_standing = ? ");
    $sql->bind_param("s", $standing); // Bind parameters
    $sql->execute(); // Execute the prepared statement
    $result = $sql->get_result(); // Get the result set

    return $result; // Return the fetched result set
}

function ClassExistingSubjectOfTheSYandSem($con)
{
    $AY = $_POST['AY'];
    $SetSem = $_POST['SetSem'];

    $classID_name = $_POST['class'];
    list($classID, $className) = explode('|', $classID_name);
    $classID = htmlspecialchars($classID);
    $className = htmlspecialchars($className);

    $findClassExistingSubjectSchedule = $con->prepare(
        "SELECT sched.schedule_id, sched.schedule_time_start, sched.schedule_time_end, sched.schedule_day, sched.schedule_semester, sched.schedule_SY, sched.teacher_id, sched.class_id, sched.subject_id, sched.room_id,
                room.room_name, room.room_type, sub.subject_name, teach.teacher_name
         FROM schedule_tb sched 
         JOIN room_tb room ON sched.room_id = room.room_id
         JOIN subject_tb sub ON sched.subject_id = sub.subject_id
         JOIN teacher_tb teach ON sched.teacher_id = teach.teacher_id
         WHERE sched.class_id = ? AND sched.schedule_SY = ? AND sched.schedule_semester = ?"
    );
    $findClassExistingSubjectSchedule->bind_param("iss", $classID, $AY, $SetSem);
    $findClassExistingSubjectSchedule->execute();
    $result = $findClassExistingSubjectSchedule->get_result();

    $schedules = [];

    // Fetch the data and group by teacher_id, class_id, subject_id, and room_id
    while ($row = $result->fetch_assoc()) {
        $key = $row['teacher_id'] . '-' . $row['class_id'] . '-' . $row['subject_id'] . '-' . $row['room_id'];

        if (!isset($schedules[$key])) {
            $schedules[$key] = $row;
            $schedules[$key]['schedule_day'] = [];
        }

        $schedules[$key]['schedule_day'][] = $row['schedule_day'];
    }

    // Concatenate the schedule_day for each group
    foreach ($schedules as $key => $schedule) {
        $schedules[$key]['schedule_day'] = implode(', ', $schedules[$key]['schedule_day']);
    }

    // Prepare the result set to return
    $resultSet = [];
    foreach ($schedules as $schedule) {
        $resultSet[] = [
            'schedule_id' => $schedule['schedule_id'],
            'schedule_time_start' => $schedule['schedule_time_start'],
            'schedule_time_end' => $schedule['schedule_time_end'],
            'schedule_day' => $schedule['schedule_day'],
            'schedule_semester' => $schedule['schedule_semester'],
            'schedule_SY' => $schedule['schedule_SY'],
            'teacher_id' => $schedule['teacher_id'],
            'class_id' => $schedule['class_id'],
            'subject_id' => $schedule['subject_id'],
            'room_id' => $schedule['room_id'],
            'room_name' => $schedule['room_name'],
            'room_type' => $schedule['room_type'],
            'subject_name' => $schedule['subject_name'],
            'teacher_name' => $schedule['teacher_name']
        ];
    }

    return $resultSet;
}

function ExistingSubjectForClass($con)
{
    $existarr = [];

    $classID_name = $_POST['class'];
    list($classID, $className) = explode('|', $classID_name);
    $classID = htmlspecialchars($classID);
    $className = htmlspecialchars($className);
    $existarr['className'] = $className;

    $subjectID_name = $_POST['subject']; // Corrected variable name
    list($subjectID, $subjectName) = explode('|', $subjectID_name);
    $subjectID = htmlspecialchars($subjectID);
    $subjectName = htmlspecialchars($subjectName);
    $existarr['subjectID'] = $subjectID;
    $existarr['subjectName'] = $subjectName;

    $findSubAndClass = $con->prepare("SELECT * FROM schedule_tb WHERE class_id = ? AND subject_id = ? AND schedule_SY = ? AND schedule_semester = ?");

    $findSubAndClass->bind_param("iiss", $classID, $subjectID, $_POST['AY'], $_POST['SetSem']);
    $findSubAndClass->execute();
    $resultfindSubAndClass = $findSubAndClass->get_result();
    if ($resultfindSubAndClass->num_rows > 0) { // Corrected num_rows
        $exist = true;
        $existarr['exist'] = $exist;
    } else {
        $exist = false;
        $existarr['exist'] = $exist;
    }
    $findSubAndClass->close(); // Close the prepared statement

    return $existarr; // Return the array
}

?>