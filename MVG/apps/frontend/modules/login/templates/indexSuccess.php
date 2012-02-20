<html>
    <head><title>Login</title></head>
    <body>
        <form action="<?php echo url_for('login/login') ?>" method="POST">
            <h3>Login</h3>
            <p>Usuario: <input type="text" name="user" /></p>
            <p>Password: <input type="text" name="password" /></p>
            <p><input type="submit" value="login" /></p>
        </form>
    </body>
</html>