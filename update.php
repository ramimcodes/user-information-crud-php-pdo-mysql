<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=users-info', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location:index.php');
    exit;
}

$statement = $pdo->prepare('SELECT * FROM users WHERE id = :id');

$statement->bindValue(':id', $id);
$statement->execute();

$user = $statement->fetch(PDO::FETCH_ASSOC);

$name = $user['name'];
$designation = $user['designation'];
$phone = $user['phone'];
$email = $user['email'];
$filedValid = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    if (!$name) {
        $filedValid['name'] = 'name is Required';
    }
    if (!$designation) {
        $filedValid['designation'] = 'desgination is Required';
    }
    if (!$phone) {
        $filedValid['phone'] = 'phone is Required';
    }
    if (!$email) {
        $filedValid['email'] = 'email is Required';
    }

    if (!is_dir('assets')) {
        mkdir('assets');
    }

    if (empty($filedValid)) {
        $image = $_FILES['image'] ?? null;
        $imagepath = $user['image'];
        if ($image && $image['tmp_name']) {
            if ($user['image']) {
                unlink($user['image']);
            }
            $imagepath = 'assets/' . randonString(8) . '/' . $image['name'];
            mkdir(dirname($imagepath));
            move_uploaded_file($image['tmp_name'], $imagepath);
        }
        $result = $pdo->prepare("UPDATE users SET image = :image, name = :name, designation = :designation, phone = :phone, email = :email WHERE id = :id");

        $result->bindValue(':image', $imagepath);
        $result->bindValue(':name', $name);
        $result->bindValue(':designation',  $designation);
        $result->bindValue(':phone', $phone);
        $result->bindValue(':email', $email);
        $result->bindValue(':id', $id);
        $result->execute();
        header('Location:index.php');
    }
}



function randonString($n)
{
    $character = '0123456789abcdefghiklmnopqrstwuvxyzABCDEFGHIJKLMNOPQRSTWUVXYZ';
    $str = '';
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($character) - 1);
        $str .= $character[$index];
    }

    return $str;
}


?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!-- component -->

<body class="font-mono bg-gray-400">
    <!-- Container -->
    <div class="container mx-auto">
        <div class="flex justify-center px-6 my-12">
            <!-- Row -->
            <div class="w-full xl:w-3/4 lg:w-11/12 flex">
                <!-- Col -->
                <div class="w-full h-auto bg-gray-400 hidden lg:block lg:w-5/12 bg-cover rounded-l-lg" style="background-image: url('https://source.unsplash.com/Mv9hjnEUHR4/600x800')"></div>
                <!-- Col -->
                <div class="w-full lg:w-7/12 bg-white p-5 rounded-lg lg:rounded-l-none">
                    <h3 class="pt-4 text-2xl text-center">Create New User!</h3>
                    <form action="" method="post" enctype="multipart/form-data" class="px-8 pt-6 pb-8 mb-4 bg-white rounded">
                        <?php if ($user['image']) : ?>
                            <div>
                                <img class="w-1/3" src="<?php echo $user['image']; ?>" alt="">
                            </div>
                        <?php endif; ?>
                        <div class="mb-4 mt-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700" for="image">
                                Upload Image
                            </label>
                            <input class="w-full px-3 py-2 mb-3 text-sm leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" id="image" type="file" name="image" />
                        </div>
                        <div class="mb-4">
                            <input class="w-full px-3 py-2 mb-3 text-sm leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" type="text" name="name" placeholder="Name" value="<?php echo $user['name']; ?>" />
                            <p class="text-red-500">
                                <?php echo $filedValid['name'] ?? ''; ?>
                            </p>
                        </div>
                        <div class="mb-4">
                            <input class="w-full px-3 py-2 mb-3 text-sm leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" type="text" name="designation" placeholder="Designation" value="<?php echo $user['designation']; ?>" />
                            <p class="text-red-500">
                                <?php echo $filedValid['designation'] ?? ''; ?>
                            </p>
                        </div>
                        <div class="mb-4">
                            <input class="w-full px-3 py-2 mb-3 text-sm leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" type="number" name="phone" placeholder="Phone" value="<?php echo $user['phone'] ?>" />
                            <p class="text-red-500">
                                <?php echo $filedValid['phone'] ?? ''; ?>
                            </p>
                        </div>
                        <div class="mb-4">
                            <input class="w-full px-3 py-2 mb-3 text-sm leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline" type="email" name="email" placeholder="Email" value="<?php echo $user['email']; ?>" />
                            <p class="text-red-500">
                                <?php echo $filedValid['email'] ?? ''; ?>
                            </p>
                        </div>
                        <div class="mb-6 text-center">
                            <button class="w-full px-4 py-2 font-bold text-white bg-blue-500 rounded-full hover:bg-blue-700 focus:outline-none focus:shadow-outline" type="submit">
                                Create
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>