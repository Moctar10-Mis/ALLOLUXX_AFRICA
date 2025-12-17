<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="header-container">
        <h1>ALOLLUXX AFRICA</h1>
        <nav>
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </nav>
    </div>
</header>
