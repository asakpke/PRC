<?php

class PRC
{
	protected $app = array(
		'name' => 'PHP RAD CRUD',
		'nameShort' => 'PRC',
		'desc' => 'PHP - PHP: Hypertext Preprocessor <br>
			RAD - Rapid Application Development <br>
			CRUD - Create, Read, Update and Delete <br>',
		'version' => '0.1.1',
		'author' => 'Aamir Shahzad',
		'path' => '/opt/lampp/htdocs/PRC',
		'pathUrl' => 'http://localhost/PRC',
	);
	protected $dbConn;
	protected $tbl = array(
		'name',
		'nameUcF',
		'isPlural' => true,
		'cols' => true,
	);
	protected $showHTML = true;

	function __construct($opt = null)
	{
		session_start();

		// START - Redirect to login page if user is not logged-in
		if (empty($_SESSION['user_id']) and empty($opt['is login page'])) {
			$msg = 'Please login to view this page.';
			$color = 'red';
			header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
			exit;
		}
		// ENDED - Redirect to login page if user is not logged-in

		$this->dbConn = mysqli_connect(
			'localhost', // servername/IP
			'root', // username
			'', // password
			'example' // Database name, notice no ","
		);

		$this->tbl['nameUcF'] = ucfirst($this->tbl['name'] ?? false);

		if ($this->showHTML) {
			?>
			<!doctype html>
			<html lang="en">
				<head>
						<meta charset="utf-8">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<meta name="description" content="<?= $this->app['desc'] ?>">
						<meta name="author" content="<?= $this->app['author'] ?>">
						<meta name="generator" content="<?= $this->app['nameShort'].' '.$this->app['version'] ?>">
						<title><?= $this->app['nameShort'].' - '.$this->app['name'] ?></title>
						<link href="<?= $this->app['pathUrl'] ?>/style.css" rel="stylesheet">
				</head>
				<body>
				<header>
					<h1><?= $this->app['name'] ?></h1>
				</header>
				<nav>
					<?php
					$dir = $this->app['path'].'/';
					$all = array_diff(scandir($dir), [".", "..", ".git"]);

					foreach ($all as $ff) {
						if (is_dir($dir . $ff)) {
							// pr($ff,'$ff');
							// pr($this->tbl['name'],'$this->tbl['name']');

							if ($ff == $this->tbl['nameUcF']) {
								echo "<strong>{$ff}</strong> | ";
							}
							else {
								echo "<a href=\"{$this->app['pathUrl']}/{$ff}\">{$ff}</a> | ";
							} // if 
						} // if is_dir
					} // foreach all
					?>
					<a href="<?= $this->app['pathUrl'] ?>/Logout.php">Logout</a>
				</nav>
			<?php
		} // if ($this->showHTML)
	} // __construct()

	function list()
	{
		$headers = '';
		$fields = '';
		$displayColCount = 0;

		foreach ($this->tbl['cols'] as $key => $tblCol) {
			if ($tblCol['is display']['on listing'] === false) {
				continue;
			}
			
			$headers .= "<th scope=\"col\">{$tblCol['display as']}</th>";
			$fields .= "{$this->tbl['name']}.{$key}, ";
			$displayColCount++;
			// Maybe limit headers/values to 10 or some columns
		} // foreach (tbl['cols'])

		$fields = rtrim($fields,', ');

		$sql = "SELECT $fields FROM {$this->tbl['name']};";
		// $sql = "SELECT $fields FROM {$this->tbl['name']} {$this->auth};";
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

		// $tbl['nameUcF'] = ucfirst($this->tbl['name']);
		?>
		<main>
			<h2><?= $this->tbl['nameUcF'] ?> <a href="<?= $this->app['pathUrl'].'/'.$this->tbl['nameUcF'] ?>/Add.php" style="text-decoration: none;">+ <small>(add new record)</small></a></h2>
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
								foreach ($this->tbl['cols'] as $key => $tblCol) {
									if ($tblCol['is display']['on listing'] === false) {
										continue;
									}
									
									echo "<td>{$row[$key]}</td>";
								} // foreach (tbl['cols'])

								echo "<td><a href=\"{$this->app['pathUrl']}/{$this->tbl['nameUcF']}/View.php\">View</a> | <a href=\"#\">Edit</a> | <a href=\"#\">Delete</a></td>";
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

	function view()
	{
		$fields = '';

		foreach ($this->tbl['cols'] as $key => $tblCol) {
			if ($tblCol['is display']['on view'] === false) {
				continue;
			}
			
			$fields .= "{$this->tbl['name']}.{$key}, ";
			// Maybe limit headers/values to 10 or some columns
		} // foreach (tbl['cols'])
		
		$fields = rtrim($fields,', ');

		$sql = "SELECT $fields FROM {$this->tbl['name']};";
		// $sql = "SELECT $fields FROM {$this->tbl['name']} {$this->auth};";
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

		// $tbl['nameUcF'] = ucfirst($this->tbl['name']);
		?>
		<main>
			<h2><?= $this->tbl['nameUcF'] ?></h2>
			
		</main>
		<?php
	} // view

	function add()
	{
		$this->pr($_POST,'$_POST');
		$tbl['name'] = ucfirst($this->tbl['isPlural'] ? rtrim($this->tbl['name'],'s') : $this->tbl['name']);
		?>
		<main>
			<h2>Add <?= $tbl['name'] ?></h2>
			<form method="post">
				<?php
				foreach ($this->tbl['cols'] as $key => $tblCol) {
					if ($tblCol['is display']['on add'] === false) {
						continue;
					}

					// $this->pr($key,'$key');
					// $this->pr($tblCol,'$tblCol');

					// switch ($key) {
					// 	case 'date':
					// 		break;
						
					// 	default:
					// 		break;
					// } // switch key
					?>
					<div>
						<label for="<?= $key ?>"><?= $tblCol['display as'] ?></label>
						<input
							type="<?= $tblCol['type'] ?>"
							name="<?= $key ?>"
							id="<?= $key ?>"
							placeholder="Enter <?= $key ?>"
							value="<?= $_POST[$key] ? $_POST[$key] : '' ?>"
							<?= $tblCol['is required'] ? 'required' : '' ?>
						>
					</div>
					<?php
				} // foreach
				?>
				<br>
				<button class="btn btn-primary py-2" type="submit">Add <?= $tbl['name'] ?></button>
			</form>
		</main>
		<?php
	} // add

	function blob($id, $name)
	{
		$stmt = mysqli_prepare(
			$this->dbConn,
			"SELECT {$this->tbl['name']}.$name FROM {$this->tbl['name']} WHERE {$this->tbl['name']}.id = ? LIMIT 1;"
			// "SELECT {$this->tbl['name']}.$name FROM {$this->tbl['name']} {$this->auth} AND {$this->tbl['name']}.id = ? LIMIT 1;"
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
			header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
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
					header("Location: {$this->app['pathUrl']}/index.php?msg={$msg}&color={$color}");
					exit();
				}
				else {
					$msg = 'Invalid email/password.';
					$color = 'red';
					header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
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

	function index()
	{
		?>
		<main>
			<p>Welcome to the index.</p>
		</main>
		<?php
	} // index()

	function logout()
	{
		$_SESSION['user_id'] = 0;
		$msg = 'You are logged out successfully.';
		$color = 'green';
		header("Location: {$this->app['pathUrl']}/Login.php?msg={$msg}&color={$color}");
		exit;
	} // logout()

	function __destruct() {
		if ($this->showHTML) {
				?>
				<footer>
					<h6><?= $this->app['nameShort'].' - '.$this->app['version'] ?> &copy; 2023</h6>
				</footer>
				</body>
				</html>
				<?php
		} // if ($this->showHTML)

		mysqli_close($this->dbConn); // optional
	} // __destruct()
} // class PRC

function pr($value,$name)
{
	$count = is_countable($value) ? count($value) : null;

	echo "<pre><h1>Name: {$name}, <small>Count:{$count}</small></h1>";
	print_r($value);
	echo '</pre>';
} // pr()