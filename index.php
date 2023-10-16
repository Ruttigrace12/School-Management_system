<?php

// Function to read students from the storage file
function getStudents()
{
    $file = 'students.json';

    if (file_exists($file)) {
        $contents = file_get_contents($file);
        $students = json_decode($contents, true);
    } else {
        $students = [];
    }

    return $students;
}

// Function to save students to the storage file
function saveStudents($students)
{
    $file = 'students.json';
    $contents = json_encode($students);
    if (file_put_contents($file, $contents) === false) {
        die('Error: Failed to save student records.');
    }
}

// Function to check if a registration number already exists
function isDuplicateRegistrationNumber($students, $registrationNumber)
{
    foreach ($students as $student) {
        if ($student['registrationNumber'] === $registrationNumber) {
            return true;
        }
    }
    return false;
}

// Add a new student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $grade = $_POST['grade'];
    $class = $_POST['class'];
    $registrationNumber = $_POST['registrationNumber'];

    $students = getStudents();

    if (!empty($name) && !empty($grade) && !empty($class) && !empty($registrationNumber)) {
        if (!isDuplicateRegistrationNumber($students, $registrationNumber)) {
            $student = [
                'name' => $name,
                'grade' => $grade,
                'class' => $class,
                'registrationNumber' => $registrationNumber
            ];
            $students[] = $student;
            saveStudents($students);
        } else {
            $errorMessage = "Error: Registration number already exists. Please enter a different registration number.";
        }
    } else {
        $errorMessage = "Error: Please fill in all the fields.";
    }
}

// Delete a student
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $students = getStudents();
    $deleteIndex = $_GET['index'];

    if (isset($students[$deleteIndex])) {
        unset($students[$deleteIndex]);
        $students = array_values($students);
        saveStudents($students);
        header('Location: index.php'); // Redirect to the main page after deleting the student
        exit;
    }
}


// Grades and classes options
$grades = ['1', '2', '3', '4', '5'];
$classes = ['A', 'B', 'C', 'D'];

$students = getStudents();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <h1>School Management</h1>

        <h2>Add Student</h2>

        <form method="POST" action="index.php">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter student name" required>
            </div>
            <div class="mb-3">
                <label for="grade" class="form-label">Grade:</label>
                <select class="form-select" id="grade" name="grade" required>
                    <option selected disabled>Select grade</option>
                    <?php foreach ($grades as $grade) : ?>
                        <option value="<?php echo $grade; ?>"><?php echo $grade; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="class" class="form-label">Class:</label>
                <select class="form-select" id="class" name="class" required>
                    <option selected disabled>Select class</option>
                    <?php foreach ($classes as $class) : ?>
                        <option value="<?php echo $class; ?>"><?php echo $class; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="registrationNumber" class="form-label">Registration Number:</label>
                <input type="text" class="form-control" id="registrationNumber" name="registrationNumber" placeholder="Enter registration number" required>
            </div>
            <button type="submit" class="btn btn-success">Add Student</button>
        </form>


        <h2>List of Students</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Class</th>
                    <th>Registration Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student) : ?>
                    <tr>
                        <td><?php echo $student['name']; ?></td>
                        <td><?php echo $student['grade']; ?></td>
                        <td><?php echo $student['class']; ?></td>
                        <td><?php echo $student['registrationNumber']; ?></td>
                        <td>
                            <a href="index.php?action=edit&index=<?php echo $index; ?>" class="btn btn-primary">Edit</a>
                            <a href="index.php?action=delete&index=<?php echo $index; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script>
</body>

</html>