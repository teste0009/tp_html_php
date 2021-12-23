<?php
show_between_pre_tag($_SERVER, "\$_SERVER");
?>

  <div class="login">
    <h2>Login</h2>
    <form action="" method="post">
      <input type="hidden" name="action" value="login">
      <label for="in_email">Email</label>
      <input type="email" name="email" id="in_email" placeholder="usuario@email.com"><br>
      <label for="in_password">Password</label>
      <input type="password" name="password" id="in_password"><br>
      <input type="submit" value="Login">
    </form>
  </div>

<?php
?>