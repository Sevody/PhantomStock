<div class="header">
        <h1 class="center">Phantom Stock</h1>
            <div class="guest">
                <? if ($logged === true) {?>
                    <a class="profile" href="profile">Profile</a>
                    <a class="logout" href="logout.php">注销</a>
                <? } else { ?>
                    <a class="login" href="login.php">登录</a>
                    <a class="signup" href="signup.php">注册</a>
                <? } ?>
        </div>
</div>