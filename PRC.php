<?php

class PRC
{
	protected $appName = 'PHP RAD CRUD';
	protected $appNameShort = 'PRC';
	protected $appDesc = 'PHP - PHP: Hypertext Preprocessor
												RAD - Rapid Application Development
												CRUD - Create, Read, Update and Delete';
	protected $appVersion = 	'0.1.0';										
	protected $appAuthor = 	'Aamir Shahzad';
	protected $appPath = '/opt/lampp/htdocs/PRC';
	protected $appPathUrl = 'http://localhost/PRC';
	protected $dbConn;
	protected $tblName;
	protected $tblCols;
	protected $showHTML = true;
	protected $auth = false;

	function __construct($opt = null)
	{
		session_start();

		// if (!empty($opt['is login page'])) {
		// 	exit;
		// }

		// START - Redirect to login page if user is not logged-in
		if (empty($_SESSION['user_id']) and empty($opt['is login page'])) {
			$msg = 'Please login to view this page.';
			$color = 'red';
			header("Location: {$this->appPathUrl}/Login.php?msg={$msg}&color={$color}");
			exit;
		}
		// ENDED - Redirect to login page if user is not logged-in

		$this->dbConn = mysqli_connect(
			'localhost', // servername/IP
			'root', // username
			'', // password
			'example' // Database name, notice no ","
		);

		if ($this->showHTML) {
			?>
			<!doctype html>
			<html lang="en">
				<head>
						<meta charset="utf-8">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<meta name="description" content="<?= $this->appDesc ?>">
						<meta name="author" content="<?= $this->appAuthor ?>">
						<meta name="generator" content="<?= $this->appNameShort.' '.$this->appVersion ?>">
						<title><?= $this->appNameShort.' - '.$this->appName ?></title>
						<link href="<?= $this->appPathUrl ?>/style.css" rel="stylesheet">
				</head>
				<body>
				<header>
					<h1><?= $this->appName ?></h1>
				</header>
				<nav>
					<?php
					$dir = $this->appPath.'/';
					$all = array_diff(scandir($dir), [".", "..", ".git"]);

					foreach ($all as $ff) {
						if (is_dir($dir . $ff)) { 
							echo "<a href=\"{$this->appPathUrl}/{$ff}\">{$ff}</a> |";
						}
					}
					?>
					<a href="<?= $this->appPathUrl ?>/Logout.php">Logout</a>
				</nav>
			<?php
		} // if ($this->showHTML)
	} // __construct()

	function list()
	{
		$headers = '';
		$fields = '';
		$displayColCount = 0;

		foreach ($this->tblCols as $key => $tblCol) {
			if ($tblCol['is display']['on listing'] === false) {
				continue;
			}
			
			$headers .= "<th scope=\"col\">{$tblCol['display as']}</th>";
			$fields .= "{$this->tblName}.{$key}, ";
			$displayColCount++;
		} // foreach (tblCols)

		$fields = rtrim($fields,', ');

		$sql = "SELECT $fields FROM {$this->tblName} {$this->auth};";
		// print_r($sql);

		$result = mysqli_query(
			$this->dbConn,
			$sql
		);

		if (mysqli_num_rows($result)) {
			$row = mysqli_fetch_assoc($result);
			$cols = array_keys($row);
			// print_r($cols);
		} // if ($result->num_rows)

		$tblNameUcF = ucfirst($this->tblName);
		?>
		<main>
			<h2><?= $tblNameUcF ?> <a href="<?= $this->appPathUrl.'/'.$tblNameUcF ?>/Add.php" style="text-decoration: none;">+ <small>(add new record)</small></a></h2>
			<table style="width:100%">
				<?php
				if (mysqli_num_rows($result)) {
					?>
					<thead>
						<tr>
							<?php
							echo $headers;
							echo '<th scope="col">Action</th>';
							?>
						</tr>
					</thead>
					<?php
				} // if ($result->num_rows)
				?>
				<tbody>
					<?php
					if (mysqli_num_rows($result)) {
						do {
							?>
							<tr>
								<?php
								foreach ($this->tblCols as $key => $tblCol) {
									if ($tblCol['is display']['on listing'] === false) {
										continue;
									}
									
									echo "<td>{$row[$key]}</td>";
								} // foreach (tblCols)

								echo '<td><a href="#">Edit</a> | <a href="#">Delete</a></td>';
								?>
							</tr>
							<?php
						} while ($row = mysqli_fetch_assoc($result));
					} // if ($result->num_rows)
					else {
						?>
						<tr><th colspan="<?= $displayColCount ?>" style="text-align:center;">No record found</th></tr>
						<?php
					} // else/if ($result->num_rows)
					?>
				</tbody>
			</table>
		</main>
		<?php
	} // list()

	function add()
	{
		echo '<pre><h1>$_POST</h1>';
		print_r($_POST);
		echo '</pre>';
		?>
		<main>
			<h2>Add <?= ucfirst(rtrim($this->tblName,'s')) ?></h2>
			<form method="post">
				<?php
				foreach ($this->tblCols as $key => $tblCol) {
					if ($tblCol['is display']['on add'] === false) {
						continue;
					}
					?>
					<div>
						<input name="<?= $key ?>" id="<?= $key ?>" placeholder="Enter <?= $key ?>">
						<label for="<?= $key ?>"><?= $tblCol['display as'] ?></label>
					</div>
					<?php
				} // foreach
				?>
				<br>
				<button class="btn btn-primary py-2" type="submit">Add <?= ucfirst(rtrim($this->tblName,'s')) ?></button>
			</form>
		</main>
		<?php
	} // add

	function blob($id, $name)
	{
		$stmt = mysqli_prepare(
			$this->dbConn,
			"SELECT {$this->tblName}.$name FROM {$this->tblName} {$this->auth} AND {$this->tblName}.id = ? LIMIT 1;"
		);

		// print_r($result);

		mysqli_stmt_bind_param($stmt, "i", $id);

		if (mysqli_stmt_execute($stmt) === true) {
			$result = mysqli_stmt_get_result($stmt);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->buffer($row[$name]);
			header("Content-type: $mimeType");
			echo $row[$name];
		} // if (mysqli_stmt_execute($stmt) === true)
	} // blob()

	function signup()
	{
		// echo '<pre><h1>$_POST</h1>';
		// print_r($_POST);
		// echo '</pre>';

		if (!empty($_POST['email'])) {
			$stmt = mysqli_prepare(
				$this->dbConn,
				'INSERT INTO users VALUES (null, ?, ?, ?);'
			);
			
			mysqli_stmt_bind_param(
				$stmt,
				"sss", // i	int, d	float, s string
				$_POST['name'],
				$_POST['email'],
				md5($_POST['password']),
			);
			
			mysqli_stmt_execute($stmt);

			$msg = 'Signup successfully, please login.';
			$color = 'green';
			header("Location: {$this->appPathUrl}/Login.php?msg={$msg}&color={$color}");
			exit;
		} // if post
		?>
		<main>
      <form method="post">
        <h2 class="">Sign Up</h2>
				<div>
          <input name="name" type="name" id="name" placeholder="name@example.com">
					<label for="name">Name</label>
        </div>
        <div>
          <input name="email" type="email" id="email" placeholder="name@example.com">
					<label for="email">Email address</label>
        </div>
        <div>
          <input name="password" type="password" class="form-control" id="password" placeholder="Password">
					<label for="password">Password</label>
        </div>
        <button type="submit">Sign Up</button>
      </form>
    </main>
		<?php
	} // signup()

	function login()
	{
		if (!empty($_POST['email'])) {
			$stmt = mysqli_prepare(
				$this->dbConn,
				"SELECT id FROM users WHERE email=? AND password=? LIMIT 1"
			);

			$_POST['password'] = md5($_POST['password']);

			$stmt->bind_param(
				"ss", 
				$_POST['email'],
				$_POST['password']
			);

			if ($stmt->execute() === true) {
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();

				if (!empty($row)) {
					$_SESSION['user_id'] = $row['id'];
					$color = 'green';
					header("Location: {$this->appPathUrl}/Dashboard.php?msg={$msg}&color={$color}");
					exit();
				}
				else {
					$msg = 'Invalid email/password.';
					$color = 'red';
					header("Location: {$this->appPathUrl}/Login.php?msg={$msg}&color={$color}");
					exit();
				}
			} // if ($stmt->execute() === true)
		} // if post
		?>
		<main>
      <form method="post">
        <h2 class="">Login</h2>
				<?php
				if (!empty($_GET['msg'])) {
					echo "<p style=\"color:{$_GET['color']}\">{$_GET['msg']}</p>";
					$_GET['msg'] = '';
					$_GET['color'] = '';
				} // if GET msg
				?>
        <div>
          <input name="email" type="email" id="email" placeholder="name@example.com">
					<label for="email">Email address</label>
        </div>
        <div>
          <input name="password" type="password" class="form-control" id="password" placeholder="Password">
					<label for="password">Password</label>
        </div>
        <button type="submit">Login</button>
				<a href="Signup.php">Signup</a>
      </form>
    </main>
		<?php
	} // login()

	function dashboard()
	{
		?>
		<main>
			<p>Welcome to the dashboard.</p>
		</main>
		<?php
	} // dashboard()

	function logout()
	{
		$_SESSION['user_id'] = 0;
		$msg = 'You are logged out successfully.';
		$color = 'green';
		header("Location: {$this->appPathUrl}/Login.php?msg={$msg}&color={$color}");
		exit;
	} // logout()

	function __destruct() {
		if ($this->showHTML) {
				?>
				<footer>
					<h6><?= $this->appNameShort.' - '.$this->appVersion ?> &copy; 2023</h6>
				</footer>
				</body>
				</html>
				<?php
		} // if ($this->showHTML)

		mysqli_close($this->dbConn); // optional
	} // __destruct()
} // class PRC