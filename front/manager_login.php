<!DOCTYPE html>
<html>
<?php include("config.php"); ?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo ($website_title); ?>
    </title>
    <script src="js/axios.min.js"></script>
    <script src="js/api.js"></script>
</head>

<body>
    <div>
        Manager Login<br>
        <input type="text" id="manager_user_textbox" placeholder="username" value="admin"><br>
        <input type="password" id="manager_password_textbox" placeholder="password" value="1234"><br>
        <button onclick="managerLogin()">Login</button><br>
        Token:<div id="token"></div>

        <script>
            function managerLogin() {
                let username = document.getElementById("manager_user_textbox").value;
                let password = document.getElementById("manager_password_textbox").value;

                let apiCallPromise = new Promise(function(resolvedThen) {
                    resolvedThen(RahMahdAPI.managerLogin(username, password));
                });

                apiCallPromise.then(function(managerLoginResponse) {
                    if (managerLoginResponse.status == 200) {
                        const COOKIE_OPTIONS = {
                            expires: new Date(Date.now() + 1000 * 60 * 60 * 24 * 1), // expires in 1 days
                            httpOnly: true,
                        };
    
                        document.cookie = 'token=' + managerLoginResponse.data.token + '; SameSite=Lax; Secure; ' + COOKIE_OPTIONS;
                        document.getElementById("token").innerHTML = managerLoginResponse.data.token;
                    }
                });
            }
        </script>
    </div>
</body>

</html>