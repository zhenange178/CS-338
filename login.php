<?php
session_start();

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Define credentials
    $credentials = [
        'admin' => '12345678'
    ];
    // Dynamically add user IDs from 100000 to 100100
    for ($i = 100000; $i <= 100100; $i++) {
        $credentials[(string)$i] = '12345678';
    }

    // Check credentials
    if (isset($credentials[$username]) && $credentials[$username] == $password) {
        $_SESSION['userID'] = $username == 'admin' ? 999999 : $username;
        echo "Login successful: userID set to " . $_SESSION['userID']; // Debug statement
        header("Location: index.php");
        exit();
    } else {
        $login_error = 'Invalid username or password';
    }
}
?>
<?php include 'includes/header.php'; ?>
<style>
    .login-container {
        background-color: #ffffff;
        padding: 20px;
        width: 500px;
        margin: 50px auto;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .login-container h2 {
        margin-bottom: 20px;
        text-align: center;
    }
    .login-container label {
        display: block;
        margin-bottom: 5px;
    }
    .login-container input {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .login-container button {
        width: 100%;
        padding: 10px;
        background-color: #E50010;
        color: #fff;
        font-weight: bold;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
    }
    .login-container button:hover {
        background-color: #0056b3;
    }
    .login-container p {
        color: red;
        margin-top: 10px;
    }
</style>

<div class="login-container">
    <h2>Login</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>
        <button type="submit">Login</button>
        <?php if ($login_error): ?>
            <p><?php echo $login_error; ?></p>
        <?php endif; ?>
    </form>
</div>

<?php include 'includes/footer.php'; ?>